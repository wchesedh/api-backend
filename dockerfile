# -----------------------------------------------------
# 1. Base Image: PHP with required extensions
# -----------------------------------------------------
    FROM php:8.2-fpm

    # Install system dependencies
    RUN apt-get update && apt-get install -y \
        git \
        curl \
        zip \
        unzip \
        libpq-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip
    
    # Install Composer
    COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
    
    # -----------------------------------------------------
    # 2. Copy app files
    # -----------------------------------------------------
    WORKDIR /var/www/html
    COPY . .
    
    # Install dependencies (no dev for production)
    RUN composer install --no-dev --optimize-autoloader
    
    # Cache configs for performance
    RUN php artisan config:cache && php artisan route:cache
    
    # -----------------------------------------------------
    # 3. Set permissions
    # -----------------------------------------------------
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
    # -----------------------------------------------------
    # 4. Expose port
    # Render will listen here
    # -----------------------------------------------------
    EXPOSE 8080
    
    # ----------------------------------
    