# Sales Business BA

## Requirements

### Option 1: Local Development
- PHP >= 8.2
- MySQL
- Composer

### Option 2: Docker Development (Recommended)
- Docker Desktop
- Docker Compose

## Installation

You can choose either Docker-based installation or traditional local installation.

### Option 1: Docker Installation (Recommended)

1. **Run the Docker setup script**
   ```sh
   chmod +x docker-setup.sh
   ./docker-setup.sh
   ```
   This will:
   - Set up all required Docker containers (PHP, MySQL, Nginx, Redis)
   - Install dependencies
   - Run migrations
   - Generate application key

2. **Configure environment**
   ```sh
   cp .env.example .env
   ```
   Update the following in your .env file:
   ```
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=builtadi
   DB_USERNAME=builtadi
   DB_PASSWORD=secret

   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

Your application will be available at http://localhost:8000

### Option 2: Traditional Installation

Follow these steps to set up the project and import the database:

1. **Clone the repository and install dependencies**
   ```sh
   composer install
   ```
2. **Copy the environment file and configure your database**
   ```sh
   cp .env.example .env
   # Edit .env and set your DB credentials
   ```
3. **Generate the application key**
   ```sh
   php artisan key:generate
   ```
4. **Import the database**

   - You can migrate and use the custom Artisan command:
     ```sh
     php artisan db:import
     ```
   - Or specify a custom SQL file:
     ```sh
     php artisan db:import --file=database/builtadi/Database.sql
     ```
   - This will import `database/builtadi/Database.sql` into your configured database.
5. **Migration**
    - Run migration prevents session issue
    ```sh
    php artisan migrate
    ```
6. **Serve the application**
   ```sh
   php artisan serve
   ```
7. **Access your API**
   - Visit: http://localhost:8000

### Usage
1. Open **Postman** (Desktop is recomended)
2. Import Postman collection **`builtadi.postman_collection.json`**

### Unit Testing

Follow these steps to configure and run the unit tests:

#### For Docker Setup
1. **Configure testing environment**
   - The test database will be automatically created in the Docker MySQL container
   - The test configuration is already set in `phpunit.xml`

2. **Run the tests**
   ```sh
   docker-compose exec app php artisan test
   ```

#### For Local Setup
1. **Configure testing environment**
   - Create a test database named `builtadi_testing` in your MySQL server
   - The test configuration is already set in `phpunit.xml`
   - Tests will use this dedicated database to avoid affecting your development data

2. **Run the tests**
   ```sh
   php artisan test
   ```

## Docker Commands Reference

### Container Management
```bash
# Start all containers
docker compose up -d

# Stop all containers
docker compose down

# View container logs
docker compose logs

# Rebuild containers
docker compose up -d --build
```

### Application Commands
```bash
# Run artisan commands
docker compose exec app php artisan <command>

# Access MySQL
docker compose exec db mysql -ubuiltadi -psecret builtadi

# Access Redis CLI
docker compose exec redis redis-cli
```

### Container Information
```bash
# List running containers
docker compose ps

# View container logs in real-time
docker compose logs -f
```
