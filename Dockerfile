FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV COMPOSER_ALLOW_SUPERUSER 1

# Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations and start
CMD ["/bin/sh", "-c", "cd /var/www/html && php artisan migrate --force 2>/dev/null || true && exec /start.sh"]
