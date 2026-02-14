<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Comic;
use App\Models\ChapterImage;

class MigrateLocalImagesToR2 extends Command
{
    protected $signature = 'media:migrate-r2';

    protected $description = 'Migrate local images to Cloudflare R2';

    public function handle()
    {
        $this->info('Starting migration...');

        $this->migratePosters();
        $this->migrateChapterImages();

        $this->info('Migration completed successfully.');
    }

    // =========================
    // Posters
    // =========================
    private function migratePosters()
    {
        $this->info('Migrating comic posters...');

        $comics = Comic::whereNotNull('poster')->get();

        foreach ($comics as $comic) {

            $oldPath = str_replace('/storage/', '', $comic->poster);

            if (!Storage::disk('public')->exists($oldPath)) {
                $this->warn("Missing poster: {$oldPath}");
                continue;
            }

            $file = Storage::disk('public')->get($oldPath);

            $filename = basename($oldPath);

            $newPath = "comics/{$comic->id}/{$filename}";

            Storage::disk('r2')->put($newPath, $file);

            $url = Storage::disk('r2')->url($newPath);

            $comic->update([
                'poster' => $url
            ]);

            $this->info("Poster migrated: Comic #{$comic->id}");
        }
    }

    // =========================
    // Chapter Pages
    // =========================
    private function migrateChapterImages()
    {
        $this->info('Migrating chapter images...');

        $images = ChapterImage::all();

        foreach ($images as $image) {

            $oldPath = $image->path;

            if (!Storage::disk('public')->exists($oldPath)) {
                $this->warn("Missing image: {$oldPath}");
                continue;
            }

            $file = Storage::disk('public')->get($oldPath);

            $filename = basename($oldPath);

            $chapter = $image->chapter;

            if (!$chapter) {
                $this->warn("Missing chapter for image ID {$image->id}");
                continue;
            }

            $newPath = "comics/{$chapter->comic_id}/chapter-id-{$chapter->id}/{$filename}";

            Storage::disk('r2')->put($newPath, $file);

            $url = Storage::disk('r2')->url($newPath);

            $image->update([
                'path' => $url
            ]);

            $this->info("Page migrated: Image #{$image->id}");
        }
    }
}
