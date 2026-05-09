<?php

// Test script to verify the export command
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Create exports directory if it doesn't exist
$exportsPath = __DIR__ . '/storage/app/exports';
if (!is_dir($exportsPath)) {
    mkdir($exportsPath, 0755, true);
}

echo "Export directory created/verified: {$exportsPath}\n";
echo "You can now run the export command:\n";
echo "php artisan export:comic --comic_id=4 --from=4 --to=47\n";
