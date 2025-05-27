# Sales Business BA
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
4. **Import the database structure and data**
   - You can use the custom Artisan command:
     ```sh
     php artisan db:import
     ```
   - Or specify a custom SQL file:
     ```sh
     php artisan db:import --file=database/builtadi/Database.sql
     ```
   - This will import `database/builtadi/Database.sql` into your configured database.
5. **Serve the application**
   ```sh
   php artisan serve
   ```
6. **Access your API**
   - Visit: http://localhost:8000

