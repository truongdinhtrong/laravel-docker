FROM composer:2.0.7 as builder
# Don't update to composer:latest because it contains some imcompability with Laravel 7
WORKDIR /app
COPY . /app
RUN composer install

FROM php:7.4.30-fpm-alpine3.16
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache
WORKDIR /var/www
COPY . /var/www
COPY --from=builder /app/vendor /var/www/vendor/

# Move all the current .env.example to .env
# All other env variables will be defined in docker-compose.yml as it takes precedent
RUN cp /var/www/.env.example /var/www/.env

RUN chmod -R 777 /var/www/storage
RUN php artisan key:generate
CMD ["php-fpm"]
