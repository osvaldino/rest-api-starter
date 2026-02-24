FROM php:8.4-fpm-alpine

LABEL maintainer="Start Kit API"

ARG WWWGROUP=1000
ARG WWWUSER=1000

WORKDIR /var/www/html

ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk add --no-cache \
        curl \
        zip \
        unzip \
        git \
        supervisor \
        nginx \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        postgresql-dev \
        linux-headers \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        gd \
        intl \
        zip \
        opcache \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS linux-headers \
    && rm -rf /var/cache/apk/* /tmp/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# OPcache: validate_timestamps=1 so file changes reflect without restart
RUN { \
        echo '[opcache]'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.save_comments=1'; \
    } > "$PHP_INI_DIR/conf.d/opcache.ini"

RUN { \
        echo '[php]'; \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
        echo 'max_execution_time=60'; \
        echo 'date.timezone=UTC'; \
        echo 'expose_php=Off'; \
    } > "$PHP_INI_DIR/conf.d/custom.ini"

COPY docker/php/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN addgroup -g $WWWGROUP appgroup \
    && adduser -u $WWWUSER -G appgroup -s /bin/sh -D appuser

RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
