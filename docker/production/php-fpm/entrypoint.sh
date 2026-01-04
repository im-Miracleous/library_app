#!/bin/bash
set -e

echo "🚀 Starting Production Environment..."

# Change to working directory
cd /var/www

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 10

# Ensure storage directories exist immediately
echo "📁 Ensuring storage directories..."
mkdir -p /var/www/storage/framework/cache /var/www/storage/framework/views /var/www/storage/framework/sessions
chmod -R 775 /var/www/storage

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Cache everything for production
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
# echo "📦 Optimizing autoloader..."
# composer dump-autoload --optimize --classmap-authoritative --no-dev

# Setup PHP-FPM Configuration
echo "⚙️  Setting up PHP-FPM config..."
# Copy custom config to utilize the 'last loaded wins' strategy (zzz-)
cp /var/www/docker/production/php-fpm/www.conf /usr/local/etc/php-fpm.d/zzz-entrypoint.conf
# Fix potentially Windows line endings
sed -i 's/\r//' /usr/local/etc/php-fpm.d/zzz-entrypoint.conf
# Remove default docker config to prevent conflict/override
rm -f /usr/local/etc/php-fpm.d/zz-docker.conf || true

# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

echo "✅ Production environment ready!"

# Start PHP-FPM (will run as www-data based on php-fpm config)
exec php-fpm
