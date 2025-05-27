# Sales Business BA
### Requirements
- php >= 8.2
- MySql

### Installation & Database Import

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
1. Open Postman (Desktop is recomended)
2. Import `builtadi.postman_collection.json`

### Unit Testing

Follow these steps to configure and run the unit tests:

1. **Configure testing environment**
   - Create a test database named `builtadi_testing` in your MySQL server
   - The test configuration is already set in `phpunit.xml`
   - Tests will use this dedicated database to avoid affecting your development data

2. **Run the tests**
   ```sh
   php artisan test
   ```
