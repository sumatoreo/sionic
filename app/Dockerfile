FROM php:7.4-fpm-buster

# Arguments defined in docker-compose.yml
ARG user
ARG uid

RUN apt-get update; \
    apt-get upgrade -y; \
    apt -y install apt-utils lsb-release apt-transport-https ca-certificates wget gnupg2 dirmngr; \
    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv E0C56BD4; \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg; \
    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -; \
    echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" | tee  /etc/apt/sources.list.d/pgdg.list; \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list; \
    echo "deb http://repo.clickhouse.tech/deb/stable/ main/" | tee /etc/apt/sources.list.d/clickhouse.list; \
    apt-get update; \
    apt-get install -y --allow-downgrades --allow-remove-essential --allow-change-held-packages \
        libmemcached-dev \
        libz-dev \
        libpq-dev  \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libmcrypt-dev \
        libonig-dev \
        clickhouse-client \
        zip \
        unzip \
        pkg-config \
        libcurl4-openssl-dev \
        libedit-dev \
        libssl-dev \
        libxml2-dev \
        git \
        curl \
        vim \
        postgresql-client-12 \
        nodejs \
        libzip-dev \
        npm \
        libmagickwand-dev \
        imagemagick; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN set -eux; \
    # Install the PHP pdo_pgsql extention
    docker-php-ext-install pdo pdo_pgsql; \
    # Install the PHP gd library
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-freetype; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'

RUN set -xe; \
    pecl channel-update pecl.php.net; \
    docker-php-ext-configure zip; \
    docker-php-ext-install zip

# Redis
RUN pecl install -o -f redis; \
    rm -rf /tmp/pear; \
    docker-php-ext-enable redis

# ImageMagick
RUN pecl install imagick; \
    docker-php-ext-enable imagick

# PgSQL
RUN docker-php-ext-install pgsql

# BcMath
RUN docker-php-ext-install bcmath

# OpCache
RUN docker-php-ext-install opcache

# MbString
RUN docker-php-ext-install mbstring

# Tokenizer
RUN docker-php-ext-install tokenizer

# XML
RUN docker-php-ext-install xml

RUN pecl install memcached-3.1.5; \
    docker-php-ext-enable memcached

# ClickHouse Client
RUN echo "deb http://repo.clickhouse.tech/deb/stable/ main/" | tee /etc/apt/sources.list.d/clickhouse.list; \
    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv E0C56BD4; \
    apt-get install -y --allow-downgrades --allow-remove-essential --allow-change-held-packages clickhouse-client; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install apidoc
RUN npm install apidoc -g

# Install composer and add its bin to the PATH.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./opcache.ini $PHP_INI_DIR/conf.d
COPY ./laravel.ini $PHP_INI_DIR/conf.d

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer; \
    chown -R $user:$user /home/$user

# Source the bash
RUN . ~/.bashrc

WORKDIR /var/www

USER $user

EXPOSE 9000

CMD ["php-fpm"]