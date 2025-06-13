ARG PHP_VERSION="8.2.0"
ARG WORKDIR=/var/www
ARG USER=$USER

FROM php:${PHP_VERSION}-fpm

USER $USER:$USER

RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
            acl \
            autoconf \
            bash \
            ca-certificates \
            curl \
            dialog \
            libfreetype6-dev \
            gcc \
            git \
            imagemagick \
            less \
            libjpeg62-turbo-dev \
            libgcrypt-dev \
            libffi-dev \
            libgsasl-dev \
            libmagickwand-dev \
            libmcrypt-dev \
            libpq-dev \
            libpng-dev \
            libwebp-dev \
            librabbitmq-dev \
            libssl-dev \
            libxml2-dev \
            libxslt-dev \
            libzip-dev \
            make \
            netcat-traditional \
            openssh-client \
            patch \
            procps \
            ssmtp \
            supervisor \
            vim \
            zip \
            unzip \
            wget && \
    pecl install -o -f amqp apcu redis && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-configure zip && \
    docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype && \
    docker-php-ext-install \
            ctype \
            gd \
            intl \
            pdo \
            pdo_pgsql \
            pgsql \
            pcntl \
            zip && \
    docker-php-ext-enable amqp apcu opcache redis zip gd && \
    curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR $WORKDIR
