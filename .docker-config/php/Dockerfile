FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
  libfreetype6-dev libjpeg62-turbo-dev \
  libmcrypt-dev libpng-dev libbz2-dev \
  libssl-dev autoconf libzip-dev \
  ca-certificates curl g++ libicu-dev \
  libonig-dev \
  && apt-get install -y --no-install-recommends libmagickwand-dev \
  && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug && \
  docker-php-ext-enable xdebug

COPY ./.docker-config/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./.docker-config/php/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

RUN docker-php-ext-install \
  bcmath bz2 exif \
  ftp gd gettext mbstring opcache \
  shmop sockets sysvmsg sysvsem sysvshm \
  zip iconv pdo_mysql intl soap

RUN docker-php-ext-configure gd \
  --with-freetype \
  --with-jpeg

RUN pecl install imagick && \
  docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/local/bin/ --filename=composer

COPY ./.docker-config/php/docker-php-settings.ini /usr/local/etc/php/conf.d/docker-php-settings.ini

RUN chown -R www-data:www-data /var/www

USER www-data

RUN mkdir -p /var/www/html/storage/runtime/mutex && \
  mkdir -p /var/www/html/storage/logs && \
  mkdir -p /var/www/html/vendor
