FROM php:8.2-apache

# Install system deps and PHP extensions for Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY . .

# Create .env and fix permissions
RUN cp .env.example .env 2>/dev/null || true \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs storage/app/public \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Fix tempnam() - set PHP sys_temp_dir to writable Laravel storage
COPY docker/php-temp.ini /usr/local/etc/php/conf.d/99-temp-dir.ini

ENV APP_ENV production
ENV APP_DEBUG false

CMD php artisan config:clear 2>/dev/null || true && \
    php artisan storage:link 2>/dev/null || true && \
    php artisan migrate --force 2>/dev/null || true && \
    php artisan db:seed --class=AdminSeeder 2>/dev/null || true && \
    php artisan db:seed --class=SchoolSeeder 2>/dev/null || true && \
    php artisan db:seed --class=DemoStudentsSeeder 2>/dev/null || true && \
    php artisan config:cache 2>/dev/null || true && \
    apache2-foreground
