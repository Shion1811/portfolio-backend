FROM php:8.4
WORKDIR /app/backend
COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME="/app/backend/compose"
ENV PATH="$PATH:/app/backend/compose/vendor/bin"
RUN apt-get update
RUN apt-get install -y zip

COPY . .
WORKDIR /app/backend/laravel_app
RUN composer install
CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]
EXPOSE 8000