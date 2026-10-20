FROM php:8.4-apache-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libzip-dev unzip \
    && docker-php-ext-install pdo_mysql mbstring zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && cp docker/apache.conf /etc/apache2/sites-available/000-default.conf \
    && printf 'expose_php=Off\ndisplay_errors=Off\nlog_errors=On\nerror_log=/proc/self/fd/2\n' > /usr/local/etc/php/conf.d/production.ini \
    && chmod +x docker/start.sh
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr SESSION_DRIVER=array CACHE_STORE=array QUEUE_CONNECTION=sync
CMD ["sh", "docker/start.sh"]
