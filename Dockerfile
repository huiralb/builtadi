FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG UID
ARG GID

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . /var/www

# Create system user to run Composer and Artisan Commands
RUN groupadd -g ${GID} dev && \
    useradd -u ${UID} -g dev -m -s /bin/bash dev

# Set permissions
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev && \
    chown -R dev:dev /var/www

USER dev
