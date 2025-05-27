#!/bin/bash

# Create necessary directories
mkdir -p docker-compose/mysql
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs

# Set proper permissions
chmod -R 777 storage bootstrap/cache

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Build and start containers
docker-compose up -d --build

# Install composer dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

echo "Docker environment is ready!"
echo "Your application is running at http://localhost:8000"
