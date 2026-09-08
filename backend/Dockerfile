FROM php:8.5-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache \
    git \
    curl \
    bash \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    autoconf \
    g++ \
    make

RUN docker-php-ext-install -j$(nproc) pdo_pgsql pgsql mbstring exif pcntl bcmath zip intl

RUN apk add --no-cache freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    || echo "GD install failed - skipping (niekrytyczne dla API)"

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

RUN addgroup -g ${GID} appgroup 2>/dev/null || true \
    && adduser -G appgroup -u ${UID} -D -s /bin/bash appuser 2>/dev/null || true \
    && chown -R appuser:appgroup /var/www/html 2>/dev/null || true

EXPOSE 9000

CMD ["php-fpm"]
