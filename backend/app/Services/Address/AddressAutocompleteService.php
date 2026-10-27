<?php

namespace App\Services\Address;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressAutocompleteService
{
    private const CACHE_PREFIX = 'adresy:v3:';

    private const STREET_TYPE_PREFIXES = [
        'ulica',
        'ul',
        'aleja',
        'aleje',
        'al',
        'plac',
        'pl',
        'osiedle',
        'os',
        'rondo',
        'r',
    ];

    /**
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    public function search(string $query): array
    {
        $query = $this->normalizeQuery($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        if (filled(config('services.api_gus.key'))) {
            $match = $this->findMatch($query);

            if ($match !== null) {
                $addresses = $this->addressesFromMatch($query, $match);

                if ($addresses !== []) {
                    return $addresses;
                }
            }
        }

        return $this->searchByLocality($query);
    }

    /**
     * @param  array<string, mixed>  $match
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    private function addressesFromMatch(string $query, array $match): array
    {
        $street = trim((string) ($match['ulica_norm'] ?? ''));
        $city = trim((string) ($match['miejscowosc'] ?? ''));
        $district = $this->normalizeDistrict($match['powiat'] ?? null);

        if ($city === '') {
            return [];
        }

        if ($street !== '') {
            return [$this->mapStreet($street, $city, $district, $match['kod_pocztowy'] ?? null)];
        }

        $streetFragment = $this->streetFragment($query, $city);
        $simc = trim((string) ($match['teryt_simc'] ?? ''));

        if ($streetFragment === '') {
            return [$this->mapCity($city, $district, $match['teryt_simc'] ?? null)];
        }

        if ($simc === '') {
            return [];
        }

        return $this->findStreets($simc, $streetFragment, $city, $district);
    }

    /**
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    private function searchByLocality(string $query): array
    {
        $cityOnlyResult = null;

        foreach ($this->candidates($query) as [$cityQuery, $streetQuery]) {
            $cities = $this->findCityCandidates($cityQuery);

            if ($cities === []) {
                continue;
            }

            if ($streetQuery === '') {
                return $this->mapCityCandidates($cities, $cityQuery);
            }

            foreach (array_slice($cities, 0, 3) as $city) {
                if ($city['sym'] === null) {
                    continue;
                }

                $streets = $this->findStreets($city['sym'], $streetQuery, $city['nazwa'], $city['powiat']);

                if ($streets !== []) {
                    return $streets;
                }
            }

            $cityOnlyResult ??= $this->mapCityCandidates($cities, $cityQuery);
        }

        return $cityOnlyResult ?? [];
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function candidates(string $query): array
    {
        $tokens = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($tokens === []) {
            return [];
        }

        $candidates = [];
        $count = count($tokens);

        for ($i = $count; $i >= 1; $i--) {
            $candidates[] = [
                $this->normalizeQuery(implode(' ', array_slice($tokens, 0, $i))),
                $this->normalizeQuery(implode(' ', array_slice($tokens, $i))),
            ];
        }

        for ($i = $count - 1; $i >= 1; $i--) {
            $candidates[] = [
                $this->normalizeQuery(implode(' ', array_slice($tokens, $i))),
                $this->normalizeQuery(implode(' ', array_slice($tokens, 0, $i))),
            ];
        }

        $unique = [];

        foreach ($candidates as [$city, $street]) {
            if ($this->containsStreetTypeToken($city)) {
                continue;
            }

            $street = $this->normalizeStreetQuery($street);
            $unique[mb_strtolower($city).'|'.mb_strtolower($street)] = [$city, $street];
        }

        return array_values($unique);
    }

    private function normalizeQuery(string $query): string
    {
        $query = str_replace([',', ';'], ' ', $query);

        return trim((string) preg_replace('/\s+/u', ' ', $query));
    }

    private function containsStreetTypeToken(string $query): bool
    {
        foreach (preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $token) {
            if (preg_match('/^(?:ul|al|pl|os|r)\./iu', $token) === 1) {
                return true;
            }

            if (in_array(mb_strtolower(rtrim($token, '.')), self::STREET_TYPE_PREFIXES, true)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeStreetQuery(string $street): string
    {
        $street = $this->normalizeQuery($street);

        do {
            $previous = $street;

            $street = $this->normalizeQuery((string) preg_replace('/^(?:ul|al|pl|os|r)\.\s*/iu', '', $street));

            $tokens = preg_split('/\s+/u', $street, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            if ($tokens !== [] && in_array(mb_strtolower(rtrim($tokens[0], '.')), self::STREET_TYPE_PREFIXES, true)) {
                array_shift($tokens);
                $street = $this->normalizeQuery(implode(' ', $tokens));
            }
        } while ($street !== $previous);

        return $street;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findMatch(string $query): ?array
    {
        $cacheKey = self::CACHE_PREFIX.'match:'.md5(mb_strtolower($query));

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $match = $this->fetchMatch($query);

        if ($match !== null) {
            Cache::put($cacheKey, $match, now()->addDay());
        }

        return $match;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchMatch(string $query): ?array
    {
        try {
            $response = $this->client()
                ->get('/match', ['q' => $query]);

            $response->throw();
        } catch (ConnectionException|RequestException $exception) {
            $this->logFailure($query, $exception);

            return null;
        }

        $data = $response->json();

        if (! is_array($data)) {
            return null;
        }

        if (isset($data['stats']['bramka'])) {
            $this->logGate('/match', $query, $data);

            return null;
        }

        $match = $data['results'][0]['match'] ?? null;

        return is_array($match) ? $match : null;
    }

    /**
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    private function findStreets(string $simc, string $fragment, string $city, ?string $district): array
    {
        $cacheKey = self::CACHE_PREFIX.'ulice:'.md5($simc.'|'.mb_strtolower($fragment));

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return array_values($cached);
        }

        $streets = $this->fetchStreets($simc, $fragment, $city, $district);

        if ($streets !== []) {
            Cache::put($cacheKey, $streets, now()->addDay());
        }

        return $streets;
    }

    /**
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    private function fetchStreets(string $simc, string $fragment, string $city, ?string $district): array
    {
        try {
            $response = $this->client()
                ->get('/ulice', ['sym' => $simc, 'q' => $fragment]);

            $response->throw();
        } catch (ConnectionException|RequestException $exception) {
            $this->logFailure($fragment, $exception);

            return [];
        }

        $streets = $response->json();

        if (! is_array($streets) || ! array_is_list($streets)) {
            $this->logGate('/ulice', $fragment, $streets);

            return [];
        }

        return collect($streets)
            ->map(function ($street) use ($city, $district) {
                if (! is_array($street)) {
                    return null;
                }

                $name = trim((string) ($street['nazwa_pelna'] ?? $street['label'] ?? ''));

                return $name !== '' ? $this->mapStreet($name, $city, $district, null) : null;
            })
            ->filter()
            ->unique(fn (array $address) => $address['street'].'|'.$address['city'])
            ->values()
            ->take(10)
            ->all();
    }

    /**
     * @return array<int, array{nazwa: string, sym: string|null, powiat: string|null, label: string}>
     */
    private function findCityCandidates(string $query): array
    {
        $cacheKey = self::CACHE_PREFIX.'miejscowosci:'.md5(mb_strtolower($query));

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return array_values($cached);
        }

        $cities = $this->fetchCities($query);

        if ($cities !== []) {
            Cache::put($cacheKey, $cities, now()->addDay());
        }

        return $cities;
    }

    /**
     * @return array<int, array{nazwa: string, sym: string|null, powiat: string|null, label: string}>
     */
    private function fetchCities(string $query): array
    {
        try {
            $response = $this->client()
                ->get('/miejscowosci', ['q' => $query]);

            $response->throw();
        } catch (ConnectionException|RequestException $exception) {
            $this->logFailure($query, $exception);

            return [];
        }

        $cities = $response->json();

        if (! is_array($cities) || ! array_is_list($cities)) {
            $this->logGate('/miejscowosci', $query, $cities);

            return [];
        }

        $results = [];

        foreach ($cities as $city) {
            if (! is_array($city)) {
                continue;
            }

            $name = trim((string) ($city['nazwa'] ?? ''));

            if ($name === '' || isset($results[$name])) {
                continue;
            }

            $label = trim((string) ($city['label'] ?? $name));

            $results[$name] = [
                'nazwa' => $name,
                'sym' => isset($city['sym']) && $city['sym'] !== '' ? (string) $city['sym'] : null,
                'powiat' => $this->normalizeDistrict($city['powiat'] ?? null),
                'label' => $label !== '' ? $label : $name,
            ];
        }

        return array_slice(array_values($results), 0, 10);
    }

    /**
     * @param  array<int, array{nazwa: string, sym: string|null, powiat: string|null, label: string}>  $cities
     * @return array<int, array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}>
     */
    private function mapCityCandidates(array $cities, string $query): array
    {
        return array_map(function (array $city) use ($query) {
            $label = mb_strtolower($city['nazwa']) === mb_strtolower($query)
                ? $city['nazwa']
                : $city['label'];

            return $this->mapCity($city['nazwa'], $city['powiat'], $city['sym'], $label);
        }, $cities);
    }

    /**
     * @return array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}
     */
    private function mapCity(string $city, ?string $district = null, mixed $simc = null, ?string $label = null): array
    {
        $label = $label !== null && $label !== '' ? $label : $city;

        return [
            'id' => 'city|'.($simc !== null ? (string) $simc : $city),
            'type' => 'city',
            'street' => null,
            'city' => $city,
            'district' => $district,
            'postal_code' => null,
            'country' => 'Polska',
            'label' => $label,
        ];
    }

    /**
     * @return array{id: string, type: 'city'|'street', street: string|null, city: string, district: string|null, postal_code: string|null, country: string, label: string}
     */
    private function mapStreet(string $street, string $city, ?string $district, mixed $postalCode): array
    {
        $postalCode = trim((string) ($postalCode ?? ''));
        $label = $postalCode !== ''
            ? sprintf('%s, %s, %s', $street, $city, $postalCode)
            : sprintf('%s, %s', $street, $city);

        return [
            'id' => 'street|'.$street.'|'.$city,
            'type' => 'street',
            'street' => $street,
            'city' => $city,
            'district' => $district,
            'postal_code' => $postalCode !== '' ? $postalCode : null,
            'country' => 'Polska',
            'label' => $label,
        ];
    }

    private function normalizeDistrict(mixed $district): ?string
    {
        $district = trim((string) ($district ?? ''));

        return $district !== '' ? $district : null;
    }

    private function streetFragment(string $query, string $city): string
    {
        $fragment = preg_replace(
            '/'.preg_quote(mb_strtolower($city), '/').'/u',
            '',
            mb_strtolower($query),
            1,
        );

        return $this->normalizeStreetQuery((string) $fragment);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.api_gus.base_url'), '/'))
            ->acceptJson()
            ->timeout(5)
            ->when(
                filled(config('services.api_gus.key')),
                fn ($http) => $http->withHeaders(['X-API-Key' => config('services.api_gus.key')]),
            );
    }

    private function logGate(string $endpoint, string $query, mixed $data): void
    {
        $gate = is_array($data) && isset($data['stats']['bramka']) ? $data['stats']['bramka'] : null;

        Log::warning('Nie udało się pobrać adresów z adresy.app — możliwy wyczerpany limit.', [
            'endpoint' => $endpoint,
            'query' => $query,
            'gate' => $gate,
        ]);
    }

    private function logFailure(string $query, ConnectionException|RequestException $exception): void
    {
        Log::warning('Nie udało się pobrać adresów z adresy.app.', [
            'query' => $query,
            'message' => $exception->getMessage(),
        ]);
    }
}
