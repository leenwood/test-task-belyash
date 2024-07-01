FROM php:8.3-fpm-alpine

RUN set -xe \
    # Git (need for composer)
    && apk add --no-cache git openssh-client shadow nodejs \
    # Fixed Intl version
    && apk add --no-cache libintl icu icu-dev libxml2-dev libpng-dev libzip-dev libpq postgresql-dev \
    && docker-php-ext-install intl \
    && apk del icu-dev \
#    && docker-php-ext-install opcache soap bcmath zip sockets pdo pdo_pgsql \
    # Clear
    && rm -rf /tmp/* /var/cache/apk/* \
    && adduser --disabled-password --uid 1000 www

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions opcache soap bcmath zip sockets pdo pdo_mysql gd amqp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY conf.d /usr/local/etc/php/conf.d

ENV APP_ENV=dev

WORKDIR /var/www/app

USER www

CMD ["php-fpm"]