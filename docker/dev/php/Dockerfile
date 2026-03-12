ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

# Starting from scratch
RUN apt-get clean && \
  apt-get -y autoremove && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install dependencies
RUN apt-get update

# Install PHP extensions
# Zip
RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip

# Git
RUN apt-get install -y git

# Curl
RUN apt-get install -y libcurl3-dev curl && docker-php-ext-install curl

# GD (image processing)
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
  docker-php-ext-configure gd --with-freetype --with-jpeg && \
  docker-php-ext-install gd

# PostgreSQL
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# BC Math
RUN docker-php-ext-install bcmath

# Intl
RUN apt-get install -y zlib1g-dev libicu-dev g++ && \
  docker-php-ext-configure intl && \
  docker-php-ext-install intl

# Custom php.ini config
COPY php.ini /usr/local/etc/php/php.ini

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer && \
  chmod 0755 /usr/bin/composer

# Clean up
RUN apt-get clean && \
  apt-get -y autoremove && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /var/www/api

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
