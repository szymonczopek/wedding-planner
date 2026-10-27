<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressAutocompleteTest extends TestCase
{
    public function test_autocomplete_requires_authentication(): void
    {
        $this->getJson('/api/addresses/autocomplete?q=krakow')
            ->assertUnauthorized();
    }

    public function test_autocomplete_validates_query(): void
    {
        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=k')
            ->assertStatus(422)
            ->assertJsonValidationErrors('q');
    }

    public function test_autocomplete_maps_full_address_match(): void
    {
        config(['services.api_gus.key' => 'test-key']);

        Http::fake([
            'api.adresy.app/api/v1/match*' => Http::response([
                'results' => [
                    [
                        'match' => [
                            'ulica_norm' => 'ul. Mała',
                            'miejscowosc' => 'Kraków',
                            'powiat' => 'Kraków',
                            'kod_pocztowy' => '31-107',
                            'teryt_simc' => '0950463',
                        ],
                    ],
                ],
            ]),
        ]);

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=kraków mała')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'street')
            ->assertJsonPath('0.street', 'ul. Mała')
            ->assertJsonPath('0.city', 'Kraków')
            ->assertJsonPath('0.district', 'Kraków')
            ->assertJsonPath('0.postal_code', '31-107')
            ->assertJsonPath('0.country', 'Polska')
            ->assertJsonPath('0.label', 'ul. Mała, Kraków, 31-107');
    }

    public function test_autocomplete_returns_city_for_city_only_query(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake([
            'api.adresy.app/api/v1/miejscowosci*' => Http::response([
                [
                    'sym' => '0918123',
                    'nazwa' => 'Warszawa',
                    'powiat' => 'Warszawa',
                    'label' => 'Warszawa (Warszawa, mazowieckie)',
                ],
            ]),
        ]);

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=warszawa')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'city')
            ->assertJsonPath('0.city', 'Warszawa')
            ->assertJsonPath('0.district', 'Warszawa')
            ->assertJsonPath('0.street', null)
            ->assertJsonPath('0.postal_code', null)
            ->assertJsonPath('0.label', 'Warszawa');

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/match'));
    }

    public function test_autocomplete_finds_streets_without_match_endpoint(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake(function (Request $request) {
            $url = $request->url();

            if (str_contains($url, '/miejscowosci')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

                return ($query['q'] ?? null) === 'kraków'
                    ? Http::response([
                        [
                            'sym' => '0950463',
                            'nazwa' => 'Kraków',
                            'powiat' => 'Kraków',
                            'label' => 'Kraków (Kraków, małopolskie)',
                        ],
                    ])
                    : Http::response([]);
            }

            if (str_contains($url, '/ulice')) {
                return Http::response([
                    [
                        'sym_ul' => '52760',
                        'nazwa_pelna' => 'ul. Mała',
                        'label' => 'ul. Mała',
                    ],
                ]);
            }

            return Http::response([]);
        });

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=kraków mał')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'street')
            ->assertJsonPath('0.street', 'ul. Mała')
            ->assertJsonPath('0.city', 'Kraków')
            ->assertJsonPath('0.district', 'Kraków')
            ->assertJsonPath('0.postal_code', null)
            ->assertJsonPath('0.label', 'ul. Mała, Kraków');

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/match'));
    }

    public function test_autocomplete_finds_street_with_partial_suffix(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake(function (Request $request) {
            $url = $request->url();

            if (str_contains($url, '/miejscowosci')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

                return ($query['q'] ?? null) === 'warszawa'
                    ? Http::response([
                        [
                            'sym' => '0918123',
                            'nazwa' => 'Warszawa',
                            'powiat' => 'Warszawa',
                            'label' => 'Warszawa (Warszawa, mazowieckie)',
                        ],
                    ])
                    : Http::response([]);
            }

            if (str_contains($url, '/ulice')) {
                return Http::response([
                    [
                        'sym_ul' => '11818',
                        'nazwa_pelna' => 'ul. Puławska',
                        'label' => 'ul. Puławska',
                    ],
                ]);
            }

            return Http::response([]);
        });

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=warszawa puła')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'street')
            ->assertJsonPath('0.street', 'ul. Puławska')
            ->assertJsonPath('0.city', 'Warszawa')
            ->assertJsonPath('0.district', 'Warszawa')
            ->assertJsonPath('0.label', 'ul. Puławska, Warszawa');
    }

    public function test_autocomplete_strips_street_prefix_and_maps_district(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake(function (Request $request) {
            $url = $request->url();

            if (str_contains($url, '/miejscowosci')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

                return ($query['q'] ?? null) === 'lublin'
                    ? Http::response([
                        [
                            'sym' => '0954700',
                            'nazwa' => 'Lublin',
                            'powiat' => 'Lublin',
                            'label' => 'Lublin (Lublin, lubelskie)',
                        ],
                    ])
                    : Http::response([]);
            }

            if (str_contains($url, '/ulice')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

                return ($query['q'] ?? null) === 'puławska'
                    ? Http::response([
                        [
                            'sym_ul' => '18102',
                            'nazwa_pelna' => 'ul. Puławska',
                            'label' => 'ul. Puławska',
                        ],
                    ])
                    : Http::response([]);
            }

            return Http::response([]);
        });

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=lublin ul. puławska')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'street')
            ->assertJsonPath('0.street', 'ul. Puławska')
            ->assertJsonPath('0.city', 'Lublin')
            ->assertJsonPath('0.district', 'Lublin')
            ->assertJsonPath('0.label', 'ul. Puławska, Lublin');

        Http::assertSent(function (Request $request) {
            if (! str_contains($request->url(), '/ulice')) {
                return false;
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['q'] ?? null) === 'puławska' && ($query['sym'] ?? null) === '0954700';
        });

        Http::assertNotSent(function (Request $request) {
            if (! str_contains($request->url(), '/miejscowosci')) {
                return false;
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return in_array($query['q'] ?? null, ['lublin ul.', 'lublin ul. puławska'], true);
        });
    }

    public function test_autocomplete_falls_back_when_match_limit_is_exhausted(): void
    {
        config(['services.api_gus.key' => 'test-key']);

        Http::fake(function (Request $request) {
            $url = $request->url();

            if (str_contains($url, '/match')) {
                return Http::response([
                    'results' => [],
                    'stats' => [
                        'bramka' => [
                            'naglowek' => 'Darmowa pula bez konta jest wspólna dla całego łącza',
                            'limit' => 10,
                            'uzyte' => 0,
                        ],
                    ],
                ]);
            }

            if (str_contains($url, '/miejscowosci')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

                return ($query['q'] ?? null) === 'kraków'
                    ? Http::response([
                        [
                            'sym' => '0950463',
                            'nazwa' => 'Kraków',
                            'powiat' => 'Kraków',
                            'label' => 'Kraków (Kraków, małopolskie)',
                        ],
                    ])
                    : Http::response([]);
            }

            if (str_contains($url, '/ulice')) {
                return Http::response([
                    [
                        'sym_ul' => '52760',
                        'nazwa_pelna' => 'ul. Mała',
                        'label' => 'ul. Mała',
                    ],
                ]);
            }

            return Http::response([]);
        });

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=kraków mał')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'street')
            ->assertJsonPath('0.street', 'ul. Mała')
            ->assertJsonPath('0.city', 'Kraków')
            ->assertJsonPath('0.district', 'Kraków');
    }

    public function test_autocomplete_falls_back_to_city_search(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake([
            'api.adresy.app/api/v1/miejscowosci*' => Http::response([
                [
                    'sym' => '0918123',
                    'nazwa' => 'Warszawa',
                    'powiat' => 'Warszawa',
                    'label' => 'Warszawa (Warszawa, mazowieckie)',
                ],
            ]),
        ]);

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=warszaw')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.type', 'city')
            ->assertJsonPath('0.city', 'Warszawa')
            ->assertJsonPath('0.district', 'Warszawa')
            ->assertJsonPath('0.label', 'Warszawa (Warszawa, mazowieckie)');
    }

    public function test_autocomplete_returns_empty_list_when_upstream_fails(): void
    {
        config(['services.api_gus.key' => null]);

        Http::fake([
            'api.adresy.app/*' => Http::response('Service unavailable', 503),
        ]);

        Sanctum::actingAs($this->createUser());

        $this->getJson('/api/addresses/autocomplete?q=warszawa')
            ->assertOk()
            ->assertExactJson([]);
    }

    private function createUser(): User
    {
        return new User([
            'name' => 'Jan Kowalski',
            'email' => 'address-autocomplete@example.com',
            'password' => 'aaaabbbb',
        ]);
    }
}
