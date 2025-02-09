FROM php:7.4-apache

USER root

WORKDIR /var/www/html

RUN apt update && apt install -y \
        vim \
        nodejs \
        npm \
        zip \
        curl \
        unzip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install opcache

RUN pecl install mailparse && docker-php-ext-enable mailparse

# Install Xdebug
# RUN pecl install -o -f xdebug && docker-php-ext-enable xdebug
# RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug

COPY docker/app/php.ini /usr/local/etc/php

COPY docker/app/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite

RUN a2enmod proxy proxy_http

CMD bash -c "composer install && sleep 35 && php /var/www/html/artisan migrate & apache2-foreground"