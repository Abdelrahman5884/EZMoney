FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y git unzip libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear || true
RUN php artisan cache:clear || true

RUN php artisan migrate --force

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
FROM php:8.2-cli

WORKDIR /app

COPY . .

# ✅ install dependencies
RUN apt-get update && apt-get install -y git unzip libzip-dev

# ✅ enable mysql
RUN docker-php-ext-install pdo pdo_mysql

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

# clear cache
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# migrate
RUN php artisan migrate --force || true

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}