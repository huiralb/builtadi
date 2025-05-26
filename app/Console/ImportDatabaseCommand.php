<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import {--file= : Path to the SQL file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a SQL file into the configured database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file') ?: base_path('database/builtadi/Database.sql');
        if (!file_exists($file)) {
            $this->error("SQL file not found: $file");
            return 1;
        }

        $db = config('database.connections.mysql');
        if (!$db) {
            $this->error('MySQL connection not configured.');
            return 1;
        }

        $user = $db['username'];
        $pass = $db['password'];
        $host = $db['host'];
        $database = $db['database'];

        $command = sprintf(
            'mysql -h%s -u%s %s %s < %s',
            escapeshellarg($host),
            escapeshellarg($user),
            $pass ? '-p'.escapeshellarg($pass) : '',
            escapeshellarg($database),
            escapeshellarg($file)
        );

        $this->info('Importing database...');
        $result = null;
        system($command, $result);
        if ($result === 0) {
            $this->info('Database import completed successfully.');
        } else {
            $this->error('Database import failed.');
        }
        return $result;
    }
}
