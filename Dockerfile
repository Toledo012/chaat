# syntax=docker/dockerfile:1
# Imagen de producción — SEMAHN Tickets (Laravel 12 · PHP 8.4)
# Targets:  app  → PHP-FPM con el código   |   web → Nginx con /public

############################
# 1) Dependencias PHP
############################
FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist \
    --optimize-autoloader --no-scripts --ignore-platform-req=ext-*

############################
# 2) Assets de Vite / Tailwind
############################
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
# Tailwind escanea las vistas de paginación de vendor/
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

############################
# 3) Runtime PHP-FPM
############################
FROM php:8.4-fpm-alpine AS app

RUN apk add --no-cache icu-libs libpng libjpeg-turbo freetype libzip \
 && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
      icu-dev libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql gd intl bcmath zip opcache \
 && apk del .build-deps

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www

COPY --chown=www-data:www-data . .
COPY --from=vendor   --chown=www-data:www-data /app/vendor       ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

# Paquetes descubiertos sin .env (no toca la BD) y carpetas escribibles
RUN php artisan package:discover --ansi \
 && mkdir -p storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/logs storage/fonts storage/app/public \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 9000
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

############################
# 4) Nginx (solo sirve /public y pasa PHP a app)
############################
FROM nginx:1.27-alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/public /var/www/public
