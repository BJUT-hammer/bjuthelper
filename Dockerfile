FROM php:7.0-apache

## APCu
RUN pecl install apcu-5.1.18 \
    && docker-php-ext-enable apcu