<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarkExistingMigrations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrations:mark-existing';

    /**
     * The console command description.
     */
    protected $description = 'Mark existing migrations as complete in the migrations table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Marking existing migrations as complete...');

        // List of existing migrations to mark as complete (excluding the 3 new view tracking ones)
        $migrationsToMark = [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000001_create_cache_table', 
            '0001_01_01_000002_create_jobs_table',
            '2026_01_17_171519_create_comics_table',
            '2026_01_17_171525_create_chapters_table',
            '2026_01_17_171532_create_pages_table',
            '2026_01_17_171627_add_is_admin_to_users_table',
            '2026_01_31_000000_create_comic_bookmarks_table',
            '2026_01_31_020000_add_role_to_users',
            '2026_02_12_200515_add_slug_to_comics_table',
            '2026_04_01_210000_add_user_id_to_comics_table',
            '2026_04_01_210001_add_user_id_to_chapters_table',
        ];

        try {
            // Check if migrations table exists
            if (!Schema::hasTable('migrations')) {
                $this->info('Creating migrations table...');
                DB::statement('
                    CREATE TABLE migrations (
                        migration VARCHAR(255) NOT NULL,
                        batch INTEGER NOT NULL,
                        PRIMARY KEY (migration)
                    )
                ');
            }

            $batch = 1;
            // Get current batch number
            $lastBatch = DB::table('migrations')->max('batch');
            if ($lastBatch) {
                $batch = $lastBatch + 1;
            }

            $markedCount = 0;
            foreach ($migrationsToMark as $migration) {
                // Check if migration already exists
                $exists = DB::table('migrations')->where('migration', $migration)->exists();
                
                if (!$exists) {
                    $this->line("Marking migration as complete: {$migration}");
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $batch
                    ]);
                    $markedCount++;
                } else {
                    $this->line("Migration already exists: {$migration}");
                }
            }

            $this->newLine();
            $this->info("✅ Successfully marked {$markedCount} existing migrations as complete!");
            $this->info("Batch number: {$batch}");
            $this->info("Total migrations processed: " . count($migrationsToMark));

            $this->newLine();
            $this->info('The following 3 new migrations were NOT marked and should be run fresh:');
            $this->line('- 2026_05_09_210000_add_view_counts_to_comics_and_chapters_table');
            $this->line('- 2026_05_09_210100_create_view_tracking_table');
            $this->line('- 2026_05_09_210200_add_default_viewer_role_to_users');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace:\n" . $e->getTraceAsString());
            return 1;
        }

        $this->newLine();
        $this->info('✅ Command completed successfully!');
        return 0;
    }
}
