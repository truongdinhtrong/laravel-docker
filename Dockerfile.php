FROM composer:2.0.7 as builder
# Don't update to composer:latest because it contains some imcompability with Laravel 7
WORKDIR /app
COPY . /app
RUN composer install

FROM php:7.4.30-fpm-alpine3.16
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache
RUN apk --update add redis mysql-client nodejs npm
RUN npm install mongo

RUN apk update && apk add --no-cache autoconf g++ make libmemcached-dev zlib-dev
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis mongodb memcached \
    && docker-php-ext-enable redis.so mongodb.so memcached.so

WORKDIR /var/www
COPY . /var/www
COPY --from=builder /app/vendor /var/www/vendor/

RUN cp /var/www/.env.example /var/www/.env

RUN chmod -R 777 /var/www/storage
RUN php artisan key:generate
CMD ["php-fpm"]
