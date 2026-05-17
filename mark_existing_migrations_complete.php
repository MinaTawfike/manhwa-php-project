<?php

/**
 * Script to mark existing migrations as complete in the migrations table
 * Excludes the 3 new view tracking migrations created on 2026_05_09
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// List of existing migrations to mark as complete (excluding the 3 new ones)
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

echo "Starting to mark existing migrations as complete...\n";

try {
    // Check if migrations table exists
    if (!Schema::hasTable('migrations')) {
        echo "Creating migrations table...\n";
        DB::statement('
            CREATE TABLE migrations (
                id VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL,
                PRIMARY KEY (id)
            )
        ');
    }

    $batch = 1;
    // Get current batch number
    $lastBatch = DB::table('migrations')->max('batch');
    if ($lastBatch) {
        $batch = $lastBatch + 1;
    }

    foreach ($migrationsToMark as $migration) {
        // Check if migration already exists
        $exists = DB::table('migrations')->where('id', $migration)->exists();
        
        if (!$exists) {
            echo "Marking migration as complete: {$migration}\n";
            DB::table('migrations')->insert([
                'id' => $migration,
                'batch' => $batch
            ]);
        } else {
            echo "Migration already exists: {$migration}\n";
        }
    }

    echo "\n✅ Successfully marked existing migrations as complete!\n";
    echo "Batch number: {$batch}\n";
    echo "Total migrations marked: " . count($migrationsToMark) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nScript completed.\n";
