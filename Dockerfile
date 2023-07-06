# Use the official PHP 8.2 image with Apache.
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Remove existing html folder from Apache directory (it's the default one, we don't need it)
RUN rm -rf /var/www/html

# Install Laravel
RUN composer create-project --prefer-dist laravel/laravel laravel

# Change Apache configuration to point to Laravel's public directory
RUN sed -i 's!/var/www/html!/var/www/laravel/public!g' /etc/apache2/sites-available/000-default.conf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy existing application directory contents
COPY . /var/www/laravel

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/laravel

# Change current directory to Laravel
WORKDIR /var/www/laravel

# Install project dependencies
RUN composer install

# Generate key and cache
RUN php artisan key:generate
RUN php artisan config:cache

# Expose port 80 and start Apache service
#EXPOSE 8000
#CMD ["apache2-foreground"]

# Expose port 8000 and start the Laravel server
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0:8000"]
