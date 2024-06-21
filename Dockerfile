FROM php:8.2.6-fpm-alpine3.18 as base

RUN apk --update add --no-cache \
    ${PHPIZE_DEPS} \
    libpng-dev \
    openssl-dev \
    gd \
    "libxml2-dev>=2.11.4" \
    git \
    "freetype>=2.10.4-r0" \
    oniguruma-dev \
    && rm -rf /var/cache/apk/*

RUN apk --update add --no-cache mpdecimal-dev

RUN pecl install decimal

RUN docker-php-ext-install \
        mbstring \
        gd \
        soap \
        xml \
        posix \
        ctype \
        pcntl

RUN pecl install -f apcu
RUN docker-php-ext-enable decimal
RUN echo 'extension=apcu.so' > /usr/local/etc/php/conf.d/30_apcu.ini
RUN chmod -R 755 /usr/local/lib/php/extensions/
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN alias composer='php /usr/bin/composer'
RUN mkdir -p /app
RUN chown -R www-data:www-data /app

WORKDIR /app

RUN apk add --update --no-cache \
    php-phpdbg \
    busybox \
    apk-tools \
    bash \
    git \
    nginx \
    supervisor \
    vim \
    libzip-dev \
    zip \
    tar \
    openssl \
    openssh \
    linux-headers \
    nodejs \
    npm \
    && rm -rf /var/cache/apk/*

RUN apk add --upgrade apk-tools \
    && apk upgrade --available

RUN docker-php-ext-install \
    bcmath \
    pdo_mysql \
    zip

RUN docker-php-ext-install opcache

ENTRYPOINT ["sh", "-c"]

FROM base

ENV XDEBUG_VERSION=3.2.2

# workdir
WORKDIR /app

COPY ./docker/config /

RUN chmod +x /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]
