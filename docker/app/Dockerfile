FROM php:8.1-apache

USER root

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    netcat-openbsd \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    default-mysql-client

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache mysqli

RUN pecl install mailparse && docker-php-ext-enable mailparse

# Install Xdebug
# RUN pecl install -o -f xdebug && docker-php-ext-enable xdebug
# RUN pecl install xdebug && docker-php-ext-enable xdebug
# RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug


COPY docker/app/php.ini /usr/local/etc/php

COPY docker/app/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite proxy proxy_http

COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# By default /var/www/html .. since its mount point.. they take owner = 1000 and group of 1001

# declare variable UID and GID
ARG UID=1000
ARG GID=1000

# If the group 1000 already exists then create a user www with id 1000 and gid 1000
# if group 1000 dont exist.. then create a group and user www with id = 1000
# add user www to list of sudoers
# after these lines /var/www/html will have owner user of www
RUN if getent group ${GID}; then \
      useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
      groupadd -g ${GID} www && \
      useradd -m -u ${UID} -g www -s /bin/bash www; \
    fi && \
    usermod -aG sudo www && \
    echo 'www ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# switch to user www, as he will be owner of /var/www/html .. and he is the user who will run apache
# so apache user will be www
USER www


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["apache2-foreground"]