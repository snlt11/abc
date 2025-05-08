FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd intl

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# Create application user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copy files and set ownership
COPY --chown=www:www . /var/www/html

# Switch to app user
USER www

# Install PHP dependencies
RUN /usr/local/bin/composer install --no-interaction --no-dev --prefer-dist

# Expose port and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]