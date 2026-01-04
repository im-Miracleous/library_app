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
    git

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create symlink for php
RUN ln -s /usr/bin/php83 /usr/bin/php

WORKDIR /var/www

CMD ["tail", "-f", "/dev/null"]
