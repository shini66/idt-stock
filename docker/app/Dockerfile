FROM node:24-bookworm-slim AS assets
WORKDIR /src
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-fpm-bookworm AS app
WORKDIR /var/www/html
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=assets /src/public/build ./public/build
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && composer check-platform-reqs --no-dev \
    && mkdir -p storage/app/public storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/idt-stock-entrypoint
RUN chmod +x /usr/local/bin/idt-stock-entrypoint
EXPOSE 9000
ENTRYPOINT ["idt-stock-entrypoint"]
CMD ["php-fpm"]

FROM nginx:alpine AS nginx-prod
COPY --from=assets /src/public /var/www/html/public
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
RUN mkdir -p /var/www/html/public/storage
EXPOSE 80
