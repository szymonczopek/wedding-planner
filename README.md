# Wedding Planner

Aplikacja do planowania ślubu: **Laravel 13 (API) + Vue 3 + PostgreSQL 18 + Nginx + Vite 6**, uruchamiana w całości przez Docker Compose.

Po uruchomieniu aplikacja jest dostępna pod adresem **http://localhost:8080**. Nie trzeba instalować lokalnie PHP, Composera, Node.js ani PostgreSQL — wszystko działa w kontenerach.

## Wymagania

- **Git**
- **Docker Engine** z pluginem **Docker Compose v2** (`docker compose version`)
- Wolny port **8080** (frontend + API przez Nginx)
- Opcjonalnie wolny port **5432** (PostgreSQL wystawiony na hosta)
- WSL 2: włączona integracja Docker Desktop z używaną dystrybucją

Sprawdzenie Dockera:

```bash
docker --version
docker compose version
```

## 1. Klonowanie repozytorium

```bash
git clone git@github.com:szymonczopek/wedding-planner.git
cd wedding-planner
```

Jeśli nie masz skonfigurowanego klucza SSH, użyj HTTPS:

```bash
git clone https://github.com/szymonczopek/wedding-planner.git
cd wedding-planner
```

## 2. Utworzenie plików środowiskowych

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

Pliki `.env.example` są już przygotowane pod ten stack:

- **`.env` (root)** — dane PostgreSQL używane przez `docker-compose.yml` oraz port aplikacji.
- **`backend/.env`** — połączenie Laravel z bazą `postgres:5432`, `APP_URL=http://localhost:8080`.
- **`frontend/.env`** — `VITE_API_BASE_URL=/api` (żądania idą przez Nginx, bez CORS).

Nie zmieniaj `DB_HOST` w `backend/.env` na `localhost` — wewnątrz sieci Dockera baza jest dostępna jako `postgres`.

Jeśli port `5432` jest zajęty na hoście, zmień w root `.env` `DB_PORT` na np. `5433` (mapowanie hosta). W `backend/.env` pozostaw `DB_PORT=5432`.

## 3. Budowa i uruchomienie kontenerów

```bash
docker compose up -d --build
```

Pierwszy build pobiera obrazy (PHP 8.5, Node 24, PostgreSQL 18, Nginx) i może potrwać kilka minut.

Sprawdź stan usług:

```bash
docker compose ps
```

Wszystkie kontenery powinny być w stanie `Up`, a `postgres` dodatkowo `healthy`. Jeśli baza jeszcze się inicjalizuje:

```bash
docker compose logs -f postgres
```

Poczekaj na komunikat `database system is ready to accept connections`.

## 4. Inicjalizacja backendu (tylko pierwszy raz)

Katalog `backend/vendor` nie jest wersjonowany, dlatego po klonie trzeba doinstalować zależności PHP:

```bash
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --seed
```

Co robią te komendy:

| Komenda | Efekt |
|---|---|
| `composer install` | instaluje zależności Laravela z `composer.lock` |
| `key:generate` | ustawia `APP_KEY` w `backend/.env` |
| `migrate --seed` | tworzy tabele i dodaje konto testowe |

Konto testowe utworzone przez seed: **`test@example.com`** / hasło **`password`**.

Jeśli nie chcesz konta testowego, uruchom samo `php artisan migrate`. Flagę `--seed` używaj tylko przy pierwszej inicjalizacji — ponowne uruchomienie seeda zakończy się błędem duplikatu e-maila.

## 5. Weryfikacja

Sprawdź API:

```bash
curl -s http://localhost:8080/api/health
```

Oczekiwana odpowiedź (przykład):

```json
{"status":"ok","php":"8.5.x","laravel":"13.x","db":"pgsql","time":"2026-10-29T12:00:00+00:00"}
```

Następnie otwórz w przeglądarce:

**http://localhost:8080**

To jedyny adres, którego należy używać. Port `5173` należy do wewnętrznej sieci Dockera i nie jest wystawiony na hosta. HMR (hot reload) również działa przez port `8080`.

## Architektura

```
Przeglądarka
    |
    v
Nginx :8080 (host) -> :80 (kontener)
    |-- /api, /sanctum, /docs, *.php  -> php:9000 (Laravel)
    |-- /storage                      -> backend/storage/app/public
    +-- /*                            -> frontend:5173 (Vite)
```

- Brak CORS — frontend i API są na tym samym originie (`/api`).
- `nginx/default.conf` jest jedynym punktem wejścia.
- Dane PostgreSQL są przechowywane w wolumenie `pgdata`.

## Przydatne komendy

| Komenda | Opis |
|---|---|
| `docker compose logs -f` | logi wszystkich usług |
| `docker compose logs -f php` | logi Laravela/PHP |
| `docker compose logs -f frontend` | logi Vite |
| `docker compose exec php bash` | shell w kontenerze PHP |
| `docker compose exec php php artisan ...` | dowolna komenda Artisan |
| `docker compose exec frontend sh` | shell w kontenerze Node |
| `docker compose restart` | restart wszystkich usług |
| `docker compose down` | zatrzymanie i usunięcie kontenerów (baza zostaje) |
| `docker compose down -v` | pełny reset, usuwa też dane PostgreSQL |

Po zmianie `backend/composer.json` lub `composer.lock`:

```bash
docker compose exec php composer install
```

Po zmianie `frontend/package.json` lub `package-lock.json`:

```bash
docker compose exec frontend npm install
docker compose restart frontend
```

Po zmianie `Dockerfile`, konfiguracji Nginx lub przy problemach z obrazami:

```bash
docker compose up -d --build
```

## Testy i jakość kodu

Testy backendu działają na bazie SQLite w pamięci, więc nie wymagają działającego PostgreSQL:

```bash
docker compose exec php php artisan test
docker compose exec php php artisan test --filter=NazwaTestu
```

Lint i analiza statyczna:

```bash
docker compose exec php ./vendor/bin/pint --test
docker compose exec php ./vendor/bin/pint
docker compose exec php ./vendor/bin/phpstan analyse --memory-limit=512M app
```

## Rozwiązywanie problemów

### Port 8080 jest zajęty

Zmień mapowanie w `docker-compose.yml` w usłudze `nginx`, np. na `"8081:80"`, a następnie:

```bash
docker compose up -d
```

Pamiętaj też o zmianie `clientPort` w `frontend/vite.config.js` (sekcja `server.hmr`) na nowy port, inaczej HMR przestanie działać.

### Port 5432 jest zajęty

W root `.env` ustaw np. `DB_PORT=5433` i uruchom ponownie:

```bash
docker compose up -d
```

`backend/.env` nadal musi mieć `DB_HOST=postgres` i `DB_PORT=5432`.

### `502 Bad Gateway` lub pusta strona

Frontend (Vite) jeszcze się uruchamia albo kontener nie wystartował:

```bash
docker compose ps
docker compose logs -f frontend
```

### Błąd połączenia z bazą danych

Sprawdź, czy `postgres` jest `healthy` oraz czy dane w root `.env` i `backend/.env` są zgodne:

```bash
docker compose ps
docker compose exec php php artisan migrate:status
```

### `Failed opening required .../vendor/autoload.php`

Nie wykonano `composer install`:

```bash
docker compose exec php composer install
```

### `No application encryption key has been specified`

Brak `APP_KEY`:

```bash
docker compose exec php php artisan key:generate
```

### Błąd 500 przy zapisie (storage/cache)

Nadaj uprawnienia do zapisu:

```bash
docker compose exec php chmod -R ug+rwX storage bootstrap/cache
docker compose exec php php artisan optimize:clear
```

### `make` nie działa

Makefile w repozytorium używa `docker.exe` (WSL). Na natywnym Linuksie i macOS używaj bezpośrednio `docker compose`. Nie uruchamiaj `make install-laravel` — backend Laravela jest już w repozytorium i ta komenda nie jest potrzebna do startu.

## Struktura repozytorium

```
.
├── backend/          # Laravel 13 API (PHP 8.5)
├── frontend/         # Vue 3 + Vite (Node 24)
├── nginx/            # konfiguracja Nginx (routing)
├── docker-compose.yml
├── .env.example      # dane DB dla Compose
└── Makefile          # skróty (głównie WSL, docker.exe)
```
