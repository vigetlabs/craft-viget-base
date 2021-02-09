FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
  libfreetype6-dev libjpeg62-turbo-dev \
  libmcrypt-dev libpng-dev libbz2-dev \
  libssl-dev autoconf libzip-dev \
  ca-certificates curl g++ libicu-dev \
  && apt-get install -y --no-install-recommends libmagickwand-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
  bcmath bz2 exif \
  ftp gd gettext mbstring opcache \
  shmop sockets sysvmsg sysvsem sysvshm \
  zip iconv pdo_mysql intl soap

RUN docker-php-ext-configure gd \
  --with-freetype-dir=/usr/include/ \
  --with-jpeg-dir=/usr/include/

RUN pecl install imagick-3.4.3 && \
  docker-php-ext-enable imagick

RUN pecl install mcrypt-1.0.3 && \
  docker-php-ext-enable mcrypt

RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/local/bin/ --filename=composer
RUN composer self-update --1
RUN composer global require hirak/prestissimo

COPY ./.docker-config/php/docker-php-settings.ini /usr/local/etc/php/conf.d/docker-php-settings.ini

RUN chown -R www-data:www-data /var/www

USER www-data

RUN mkdir -p /var/www/html/storage/runtime/mutex && \
  mkdir -p /var/www/html/storage/logs && \
  mkdir -p /var/www/html/vendor
