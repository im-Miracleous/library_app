#!/bin/bash
set -e

echo "🚀 Starting Development Environment..."

# Change to root for setup tasks
cd /var/www

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 5

# Install/update composer dependencies (as current user)
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    echo "✅ Composer dependencies already installed"
fi

# Install/update npm dependencies (as current user)
if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm install
else
    echo "✅ NPM dependencies already installed"
fi

# Generate app key if not exists
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Clear and cache config for development
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Set proper permissions (run as root)
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

echo "✅ Development environment ready!"

# Start PHP-FPM (will run as www-data based on php-fpm config)
exec php-fpm
