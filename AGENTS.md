# AGENTS.md

## Stack & Architecture

- **PHP 8.5 + Laravel 13 (API) + Vue 3 Composition API + Postgres 18 + Nginx + Vite 6** — all via Docker Compose, single exposed port `8080:80` (README says `80`, compose actually maps `8080`).
- Nginx (`nginx/default.conf`) is the only entry: `http://localhost:8080`
  - `/api/*` + `*.php`/`/storage/*` → `php:9000` (Laravel `backend/public`)
  - `/*` → `frontend:5173` (Vite dev server, proxy + HMR)
- No CORS — frontend `baseURL` is `/api` (same origin). See `frontend/src/api/client.js:14`. Override via `VITE_API_BASE_URL` (`frontend/.env.example`, root `.env`).
- DB: `postgres:5432` inside `app_network`; from host use `localhost:5432`. Service names (`php`, `frontend`, `postgres`) are Docker-internal only.
- Vite HMR: `frontend/vite.config.js:23` sets `clientPort: 8080`, `usePolling: true`. Do not change `VITE_API_BASE_URL` to `http://php:9000` — browser cannot resolve it.

## Directory Layout

- `backend/` — Laravel app (entry: `routes/api.php`, `app/`). Has its own `AGENTS.md`/`CLAUDE.md` (bootstrap placeholder for `laravel/boost` — not yet installed).
- `frontend/` — Vue 3 SPA (`src/main.js`, `src/api/client.js`, `src/views/`). Alias `@` → `src/` (`frontend/vite.config.js:8`).
- `nginx/default.conf` — routing truth; `docker-compose.yml` — service truth.

## Setup & Run

```bash
cp .env.example .env
cp backend/.env.example backend/.env   # then set DB_CONNECTION=pgsql, DB_HOST=postgres, DB_PORT=5432, DB_DATABASE/USERNAME/PASSWORD=wedding/secret
cp frontend/.env.example frontend/.env

# Native Linux:
docker compose up -d --build
# WSL (Makefile uses this):
docker.exe compose up -d --build
# or: make up  (currently hardcoded to docker.exe — use docker on native Linux)

# First-time Laravel init (only if backend/ is empty):
make install-laravel  # = composer create-project laravel:13 + key:generate + migrate
```

Verify: `curl http://localhost:8080/api/health` (`backend/routes/api.php:10`), open `http://localhost:8080`.

## Guardrails

- **NEVER run migrations or seeds yourself.** Do not execute `php artisan migrate`, `migrate:refresh`, `migrate:fresh`, `migrate:rollback`, `db:seed`, or their Makefile wrappers `make migrate` / `make fresh` / `make install-laravel` without explicit user request. Ask the user to run them if needed. Reading/creating migration files is fine.

## Commands

- `docker compose logs -f [php|frontend|nginx]` / `make logs[-php|-frontend|-nginx]`
- `docker compose exec php bash` / `make shell-php` — run artisan/composer inside container
- Migrations (user-only): `make migrate` (= `php artisan migrate`), `make fresh` (= `migrate:fresh --seed`) — agents must not run these on their own
- `docker compose exec php php artisan test` or `composer test` (clears config, runs phpunit) — tests use `sqlite :memory:` (`backend/phpunit.xml:26`), no Postgres needed. Single test: `docker compose exec php php artisan test --filter=TestName`
- Backend code style & static analysis (PHP 8.5 in container — host is 8.3, must run inside `php`):
  - From host (root): `make pint` (= `docker compose exec php ./vendor/bin/pint --test`) / `make pint-fix` / `make phpstan` (= `phpstan analyse --memory-limit=512M app`) — `laravel/pint ^1.30` / `phpstan ^2.2` (`backend/composer.json:18,21`)
  - Inside container (`/var/www/html`, i.e. `backend/`): `make pint` / `make pint-fix` / `make phpstan` via `backend/Makefile:1` (runs `vendor/bin/*` directly, no `docker compose exec`). Same `phpstan` default: analyses `app/` at level 0 (no config yet).
- Frontend: `docker compose exec frontend npm run dev` / `build` (`frontend/package.json:7`). No test runner configured.
- Host fallback (if PHP/Node installed locally and version >=8.4): `composer install` in `backend/`, `npm install && npm run dev` in `frontend/` — do not run migrations from host either.

## Env & Config Gotchas

- Root `.env` is source for compose DB vars (`POSTGRES_DB/USER/PASSWORD`); `backend/.env` must match (`DB_HOST=postgres` in Docker, `localhost` from host). `backend/.env.example` defaults to `sqlite` — must be switched to `pgsql` for Docker.
- `backend/.env` `APP_URL` should be `http://localhost:8080` (not `8000`) when running behind nginx.
- Compose volumes: `backend` mounted `:cached` to `/var/www/html`, `frontend` to `/app` with anonymous `/app/node_modules`. Edits on host reflect instantly.
- `make` targets use `docker.exe` — fails on native Linux; substitute `docker`.

## Conventions

- API routes go in `backend/routes/api.php` (already has `/health` and `/users`). Frontend calls via `api` from `frontend/src/api/client.js`.
- Keep `VITE_API_BASE_URL=/api` — do not hardcode hostnames.
