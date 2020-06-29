FROM php:7.3-alpine

COPY . /

RUN apk add --no-cache \
        git \
        openssh-client \
        make \
        bash \
    ;

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www

CMD php -S 0.0.0.0:80 /var/www/public/index.php
