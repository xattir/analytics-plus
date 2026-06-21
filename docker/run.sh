#!/bin/sh

cd /var/www

# Fix ownership for volumes mounted at runtime
chown -R www:www-data /var/www/storage
chmod -R 775 /var/www/storage
chmod 775 /var/www/bootstrap/cache

# Ensure all log directories exist
mkdir -p /var/www/storage/logs/whatsapp
mkdir -p /var/www/storage/logs/orders

# Run database migrations
php artisan migrate --force

# Cache config/routes/views for performance (needs runtime env vars like APP_KEY)
php artisan optimize

# Start supervisor (nginx + php-fpm + queue workers)
/usr/bin/supervisord -c /etc/supervisord.conf