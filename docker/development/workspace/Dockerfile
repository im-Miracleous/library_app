FROM node:20-alpine

# Install PHP and Composer for running artisan commands
RUN apk add --no-cache \
    php83 \
    php83-phar \
    php83-mbstring \
    php83-openssl \
    php83-tokenizer \
    php83-xml \
    php83-dom \
    php83-xmlwriter \
    php83-xmlreader \
    curl \
    git \
    shadow

# Arguments defined in docker-compose.yml
ARG user=laravel
ARG uid=1000

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to match host user
RUN if getent passwd $uid; then userdel -f $(getent passwd $uid | cut -d: -f1); fi && \
    if getent group $uid; then groupdel $(getent group $uid | cut -d: -f1); fi && \
    useradd -G root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Create symlink for php
RUN ln -s /usr/bin/php83 /usr/bin/php

# Set permissions for global directories that might be touched
RUN mkdir -p /var/www && chown -R $user:$user /var/www

USER $user

WORKDIR /var/www

CMD ["tail", "-f", "/dev/null"]
