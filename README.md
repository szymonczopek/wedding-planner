# Wedding Planner - Docker Stack

DEV stack: **PHP 8.5 + Laravel 13 (API only) + Vue 3 Composition API + Postgres 18 + Nginx**

Połączenie Frontend <-> Backend: **Opcja 1 - Proxy przez Nginx (brak CORS)**


## Szybki start

```bash
# 1. Skopiuj env
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# 2. Uruchom
docker compose up -d --build
# lub (WSL): docker.exe compose up -d --build

# 3. Zainstaluj Laravel (jeśli backend pusty)
make install-laravel
# lub ręcznie:
docker compose exec php composer create-project laravel/laravel:^13.0 /tmp/laravel
docker compose exec php bash -c "cp -rn /tmp/laravel/. /var/www/html/ && php artisan key:generate"

# 4. Sprawdź
curl http://localhost/api/health
open http://localhost
```

## Nginx

`nginx/default.conf` - serwuje `backend/public` i proxy `location /` do Vite. HMR skonfigurowane z `clientPort: 80`.

## Przydatne komendy

```bash
docker compose logs -f
docker compose exec php bash
docker compose exec php php artisan migrate
docker compose down -v # usuń też pgdata
```
