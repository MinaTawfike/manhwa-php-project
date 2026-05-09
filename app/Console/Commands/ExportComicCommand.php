<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chapter;
use App\Models\ChapterImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ExportComicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:comic {--comic_id= : Comic ID to export} {--from= : Starting chapter number} {--to= : Ending chapter number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export chapters and images for a specific comic to SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $comicId = $this->option('comic_id');
        $fromChapter = $this->option('from');
        $toChapter = $this->option('to');

        if (!$comicId || !$fromChapter || !$toChapter) {
            $this->error('All options are required: --comic_id, --from, --to');
            return 1;
        }

        $this->info("Exporting comic ID {$comicId}, chapters {$fromChapter} to {$toChapter}...");

        // Create exports directory if it doesn't exist
        $exportsPath = storage_path('app/exports');
        if (!File::exists($exportsPath)) {
            File::makeDirectory($exportsPath, 0755, true);
        }

        // Generate filename
        $filename = "comic_{$comicId}_chapters_{$fromChapter}_{$toChapter}.sql";
        $filepath = $exportsPath . '/' . $filename;

        // Start building SQL content
        $sqlContent = $this->generateSqlContent($comicId, $fromChapter, $toChapter);

        // Write to file
        File::put($filepath, $sqlContent);

        $this->info("SQL export completed: {$filename}");
        $this->info("File saved to: {$filepath}");

        return 0;
    }

    private function generateSqlContent($comicId, $fromChapter, $toChapter)
    {
        $sql = "-- Comic Export SQL File\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- Comic ID: {$comicId}, Chapters: {$fromChapter} to {$toChapter}\n";
        $sql .= "-- Target: InfinityFree phpMyAdmin\n\n";

        // Disable foreign key checks
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Get chapters to export
        $chapters = Chapter::where('comic_id', $comicId)
            ->whereBetween('number', [$fromChapter, $toChapter])
            ->orderBy('number')
            ->get();

        if ($chapters->isEmpty()) {
            $sql .= "-- No chapters found for the specified criteria\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            return $sql;
        }

        $chapterIds = $chapters->pluck('id')->toArray();

        // Add cleanup statements (optional but recommended)
        $sql .= "-- Cleanup existing data\n";
        $sql .= "DELETE FROM `chapter_images` WHERE `chapter_id` IN (" . implode(',', $chapterIds) . ");\n";
        $sql .= "DELETE FROM `chapters` WHERE `comic_id` = {$comicId} AND `number` BETWEEN {$fromChapter} AND {$toChapter};\n\n";

        // Export chapters
        $sql .= "-- ========================================\n";
        $sql .= "-- CHAPTERS EXPORT\n";
        $sql .= "-- ========================================\n";
        $sql .= "INSERT INTO `chapters` (`id`, `comic_id`, `user_id`, `name`, `number`, `rating`, `comment`, `created_at`, `updated_at`) VALUES\n";

        $chapterValues = [];
        foreach ($chapters as $chapter) {
            $values = [
                $chapter->id,
                $chapter->comic_id,
                $chapter->user_id ?: 'NULL',
                $chapter->name ? "'" . addslashes($chapter->name) . "'" : 'NULL',
                $chapter->number,
                $chapter->rating,
                $chapter->comment ? "'" . addslashes($chapter->comment) . "'" : 'NULL',
                "'" . $chapter->created_at . "'",
                "'" . $chapter->updated_at . "'"
            ];
            $chapterValues[] = '(' . implode(',', $values) . ')';
        }

        $sql .= implode(",\n", $chapterValues) . ";\n\n";

        // Export chapter images
        $sql .= "-- ========================================\n";
        $sql .= "-- CHAPTER IMAGES EXPORT\n";
        $sql .= "-- ========================================\n";
        
        $images = ChapterImage::whereIn('chapter_id', $chapterIds)
            ->orderBy('chapter_id')
            ->orderBy('page_number')
            ->get();

        if ($images->isNotEmpty()) {
            $sql .= "INSERT INTO `chapter_images` (`id`, `chapter_id`, `path`, `page_number`, `alt`, `created_at`, `updated_at`) VALUES\n";

            $imageValues = [];
            foreach ($images as $image) {
                $values = [
                    $image->id,
                    $image->chapter_id,
                    "'" . addslashes($image->path) . "'",
                    $image->page_number,
                    $image->alt ? "'" . addslashes($image->alt) . "'" : 'NULL',
                    "'" . $image->created_at . "'",
                    "'" . $image->updated_at . "'"
                ];
                $imageValues[] = '(' . implode(',', $values) . ')';
            }

            $sql .= implode(",\n", $imageValues) . ";\n\n";
        } else {
            $sql .= "-- No images found for the specified chapters\n\n";
        }

        // Re-enable foreign key checks
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Add verification queries
        $sql .= "\n-- ========================================\n";
        $sql .= "-- VERIFICATION QUERIES\n";
        $sql .= "-- ========================================\n";
        $sql .= "-- Run these queries after import to verify data:\n";
        $sql .= "-- SELECT COUNT(*) as chapters_exported FROM chapters WHERE comic_id = {$comicId} AND number BETWEEN {$fromChapter} AND {$toChapter};\n";
        $sql .= "-- SELECT COUNT(*) as images_exported FROM chapter_images WHERE chapter_id IN (" . implode(',', $chapterIds) . ");\n";

        return $sql;
    }
}
