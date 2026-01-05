FROM php:8.4-fpm AS production

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    ca-certificates \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Download and install the PHP extension installer (fast build)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd intl opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Entrypoint will run as root, PHP-FPM will switch to www-data
# USER directive removed to allow entrypoint flexibility
