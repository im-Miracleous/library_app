# ==============================================================================
# Stage 1: Base (Shared Runtime)
# ==============================================================================
FROM php:8.4-fpm-alpine AS base

# Arguments defined in docker-compose.yml
ARG user=laravel
ARG uid=1000

# Install system dependencies (Runtime only)
# shadow: for useradd/usermod
# bash: for entrypoint scripts
# libpng, libjpeg-turbo, freetype: for GD
# libzip: for zip
# icu-libs: for intl
RUN apk add --no-cache \
    shadow \
    bash \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs

# Install PHP extensions
# We rely on mlocati installer for ease of use, but in a pure alpine optimization 
# we could use 'docker-php-ext-install' to reduce build deps further. 
# For now, this is a good balance.
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd intl opcache

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user /var/www

WORKDIR /var/www

# ==============================================================================
# Stage 2: Builder (Dependencies)
# ==============================================================================
FROM base AS builder
ARG user=laravel

# Install build dependencies (Git, Zip, Curl)
# These are needed for Composer but not for the final app
RUN apk add --no-cache git curl zip unzip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Switch to user for safe install
USER $user

# Copy dependency files
COPY --chown=$user:$user composer.json composer.lock ./

# Install dependencies (No Dev, Optimized)
# Install dependencies (No Dev, Optimized)
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts

# ==============================================================================
# Stage 3: Development (Local Dev)
# ==============================================================================
FROM base AS development
ARG user=laravel

# Install Dev tools if needed (e.g. XDebug, though often separate)
# For now, we just need Composer for 'composer install' inside the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install git/zip for comfortable dev experience inside container
RUN apk add --no-cache git zip unzip curl

USER $user
# We don't COPY source here because it's volume mounted
# We don't RUN composer install because entrypoint does it
USER root

# ==============================================================================
# Stage 4: Production (Final Artifact)
# ==============================================================================
FROM base AS production
ARG user=laravel

# Copy Composer binary for dump-autoload
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Dependencies from Builder
COPY --chown=$user:$user --from=builder /var/www/vendor /var/www/vendor

# Copy Application Code
COPY --chown=$user:$user . .

# Switch to user
USER $user

# Optimize Autoloader (Final Step)
RUN composer dump-autoload --optimize

USER root
