FROM php:8.2-cli-bookworm AS vendor

WORKDIR /var/www/html

RUN apt-get update
RUN apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev
RUN docker-php-ext-install intl pdo_mysql zip bcmath
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM node:22-bookworm AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=vendor /var/www/html/vendor ./vendor
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.2-cli-bookworm

WORKDIR /var/www/html

RUN apt-get update
RUN apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev
RUN docker-php-ext-install intl pdo_mysql zip bcmath
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && mkdir -p storage/app/public/fields \
    && cp public/landing/futsal-court.png storage/app/public/fields/gor-pertamina-futsal.png \
    && cp public/landing/badminton-court.png storage/app/public/fields/arena-badminton-mawar.png \
    && cp public/landing/football-stadium.jpg storage/app/public/fields/desi-stadium.jpg \
    && cp public/landing/basketball-court.jpg storage/app/public/fields/lapangan-basket-kota.jpg \
    && cp public/landing/tennis-court.png storage/app/public/fields/tennis-club-merdeka.png \
    && cp public/landing/volleyball-court.png storage/app/public/fields/volly-arena-malang.png \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan storage:link --force \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan view:cache \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
