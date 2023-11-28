ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-fpm as testtask-php-fpm

# VOLUME ../../

ARG APPDIR=/var/www/testtask

ARG USER=www-data
ARG USER_ID=1000
ARG GROUP=www-data
ARG GROUP_ID=1000

# change uid and gid values for ${USER}
RUN if getent passwd ${USER}; then \
    userdel -f ${USER} &&\
    if getent group ${GROUP} ; then groupdel ${GROUP}; fi &&\
    groupadd -g ${GROUP_ID} ${GROUP} &&\
    useradd -l -u ${USER_ID} -g ${GROUP} ${USER} &&\
    install -d -m 0755 -o ${USER} -g ${GROUP} /var/www &&\
    chown --changes --silent --no-dereference --recursive \
          --from=33:33 ${USER_ID}:${GROUP_ID} \
        /var/www \
;fi

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libpq-dev zip libpng-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd

RUN apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; rm -rf /var/lib/apt/lists/*

WORKDIR ${APPDIR}

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN curl -sS https://get.symfony.com/cli/installer | bash

USER ${USER}
# Application
# RUN composer install
# RUN composer install && \
# php bin/console doctrine:database:create && \
# php bin/console doctrine:migrations:migrate
# php bin/console app:references:fill-data --verbose
