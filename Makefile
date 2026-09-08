.PHONY: up down build logs shell-php shell-node install-laravel migrate fresh test pint pint-fix phpstan stan

up:
	docker.exe compose up -d

down:
	docker.exe compose down

build:
	docker.exe compose build --no-cache

logs:
	docker.exe compose logs -f

logs-nginx:
	docker.exe compose logs -f nginx

logs-php:
	docker.exe compose logs -f php

logs-frontend:
	docker.exe compose logs -f frontend

shell-php:
	docker.exe compose exec php bash

shell-node:
	docker.exe compose exec frontend sh

install-laravel:
	docker.exe compose exec php bash -c "composer create-project laravel/laravel:^13.0 /tmp/laravel && cp -rn /tmp/laravel/. /var/www/html/ && rm -rf /tmp/laravel && php artisan key:generate && php artisan migrate"

migrate:
	docker.exe compose exec php php artisan migrate

fresh:
	docker.exe compose exec php php artisan migrate:fresh --seed

pint:
	docker.exe compose exec php ./vendor/bin/pint --test

pint-fix:
	docker.exe compose exec php ./vendor/bin/pint

phpstan:
	docker.exe compose exec php ./vendor/bin/phpstan analyse --memory-limit=512M app

stan: phpstan

health:
	curl -s http://localhost/api/health | jq . || curl -s http://localhost/api/health
	@echo ""
	curl -s http://localhost/ | head -n 20
