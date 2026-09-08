# Laravel 11 dev image for ICTSchool (and the ICTHospital rebuild).
# Base image php:8.3-cli is already pulled locally, so this builds fast.
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
        libonig-dev libxml2-dev libicu-dev default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring zip gd bcmath intl exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
EXPOSE 8000

# Keep the container alive; we drive artisan/composer with `docker compose exec`.
CMD ["sleep", "infinity"]
