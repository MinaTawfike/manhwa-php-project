<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SqliteToMysqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Laravel system tables to exclude
        $excludeTables = [
            'migrations',
            'cache', 
            'cache_locks', 
            'jobs', 
            'failed_jobs', 
            'sessions', 
            'password_reset_tokens'
        ];

        try {
            // Get all tables from SQLite dynamically
            $sqliteTables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table'");
            
            $tablesToMigrate = [];
            
            foreach ($sqliteTables as $table) {
                $tableName = $table->name;
                
                // Skip excluded tables
                if (!in_array($tableName, $excludeTables)) {
                    $tablesToMigrate[] = $tableName;
                }
            }

            $this->command->info('Found ' . count($tablesToMigrate) . ' tables to migrate: ' . implode(', ', $tablesToMigrate));

            // Disable foreign key checks for MySQL
            DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tablesToMigrate as $table) {
                $this->command->info("Migrating table: {$table}");
                
                try {
                    // Get data from SQLite
                    $data = DB::connection('sqlite')->table($table)->get();
                    
                    if ($data->isEmpty()) {
                        $this->command->warn("Table {$table} is empty, skipping...");
                        continue;
                    }

                    // Clear existing data in MySQL table
                    DB::connection('mysql')->table($table)->delete();

                    // Insert data into MySQL
                    $insertedCount = 0;
                    foreach ($data as $row) {
                        try {
                            DB::connection('mysql')->table($table)->insert((array) $row);
                            $insertedCount++;
                        } catch (\Exception $e) {
                            $this->command->error("Error inserting row into {$table}: " . $e->getMessage());
                            Log::error("Migration error for table {$table}: " . $e->getMessage(), [
                                'row' => (array) $row
                            ]);
                        }
                    }
                    
                    $this->command->info("Successfully migrated {$insertedCount} records from {$table}");
                    
                } catch (\Exception $e) {
                    $this->command->error("Error migrating table {$table}: " . $e->getMessage());
                    Log::error("Migration error for table {$table}: " . $e->getMessage());
                }
            }

            // Re-enable foreign key checks
            DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
            
            $this->command->info('Data migration completed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Migration failed: ' . $e->getMessage());
            Log::error('Migration failed: ' . $e->getMessage());
            
            // Ensure foreign key checks are re-enabled even on error
            try {
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $e) {
                // Ignore if this fails
            }
            
            throw $e;
        }
    }
}
