FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk update && apk add --no-cache bash \
    git \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

# Install PHP extensions
RUN pecl install zip \
    && docker-php-ext-enable zip \
    && docker-php-ext-install pdo_mysql mbstring intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data ./laravel /var/www/html

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]