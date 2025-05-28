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
docker compose up -d --build

# Wait for database to be ready
echo "Waiting for database connection..."
while ! docker compose exec db mysqladmin --host=db --user=root --password="${DB_PASSWORD}" ping --silent &> /dev/null ; do
    echo -n "."
    sleep 2
done
echo -e "\nDatabase is ready!"

# Install composer dependencies
docker compose exec app composer install

# Generate application key
docker compose exec app php artisan key:generate

# Wait a bit more to ensure MySQL is fully ready
sleep 5

# drop all tables
docker compose exec app php artisan db:wipe

# Run migrations
docker compose exec app php artisan migrate

echo "Docker environment is ready!"
echo "Your application is running at http://localhost:8000"
