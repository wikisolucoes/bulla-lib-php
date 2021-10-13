FROM php:7.3-fpm

ARG uid=1000
ARG user=gestor

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/bin/composer

RUN apt-get update -y && apt-get install -y --no-install-recommends git curl apt-utils zlib1g-dev libzip-dev unzip zip \
	libxml2-dev && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install mysqli json xml pdo_mysql

RUN useradd -G www-data,root -u $uid -d /home/$user $user && mkdir -p /home/$user && chown $user:$user /home/$user && chown $user:$user /var/www/html

WORKDIR /var/www/html

USER $user
