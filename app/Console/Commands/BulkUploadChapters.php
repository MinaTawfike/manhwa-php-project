<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\Comic;
use App\Services\ChapterUploadService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class BulkUploadChapters extends Command
{
    protected ChapterUploadService $chapterUploadService;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'chapters:bulk-upload 
                           {--comic_id= : Comic ID to upload chapters to}
                           {--path=storage/app/bulk_chapters : Base directory containing chapter folders}
                           {--skip-existing : Skip chapters that already exist}';

    /**
     * The console command description.
     */
    protected $description = 'Bulk upload manga chapters from local directory to database and storage';

    /**
     * Statistics for the upload process
     */
    protected array $stats = [
        'chapters_found' => 0,
        'chapters_uploaded' => 0,
        'chapters_skipped' => 0,
        'images_uploaded' => 0,
        'images_skipped' => 0,
        'errors' => 0,
    ];

    public function __construct(ChapterUploadService $chapterUploadService)
    {
        parent::__construct();
        $this->chapterUploadService = $chapterUploadService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $comicId = $this->option('comic_id');
        $basePath = $this->option('path');
        $skipExisting = $this->option('skip-existing');

        // Validate inputs
        $validator = Validator::make([
            'comic_id' => $comicId,
            'path' => $basePath,
        ], [
            'comic_id' => 'required|integer|exists:comics,id',
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        // Verify comic exists
        $comic = Comic::find($comicId);
        if (!$comic) {
            $this->error("Comic with ID {$comicId} not found.");
            return 1;
        }

        // Normalize path
        $fullPath = base_path($basePath);
        if (!is_dir($fullPath)) {
            $this->error("Directory not found: {$fullPath}");
            return 1;
        }

        // Check for comic-specific directory structure
        $comicPath = $fullPath . '/' . $comicId;
        if (!is_dir($comicPath)) {
            $this->error("Comic directory not found: {$comicPath}");
            $this->info("Expected structure: storage/app/bulk_chapters/{$comicId}/");
            $this->info("With subdirectories: Chapter 1/, Chapter 2/, etc.");
            return 1;
        }

        $this->info("Starting bulk upload for comic: {$comic->title} (ID: {$comicId})");
        $this->info("Source directory: {$comicPath}");
        $this->info("Skip existing: " . ($skipExisting ? 'YES' : 'NO'));
        $this->newLine();

        // Get all chapter directories
        $chapterDirs = $this->getChapterDirectories($comicPath);
        $this->stats['chapters_found'] = count($chapterDirs);

        if (empty($chapterDirs)) {
            $this->warn('No chapter directories found.');
            return 0;
        }

        $this->info("Found {$this->stats['chapters_found']} chapter directories");

        // Create progress bar
        $progressBar = new ProgressBar($this->output, $this->stats['chapters_found']);
        $progressBar->start();

        // Process each chapter directory
        foreach ($chapterDirs as $chapterDir) {
            try {
                $this->processChapterDirectory($chapterDir, $comic, $skipExisting);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error("Error processing {$chapterDir['name']}: {$e->getMessage()}");
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->displayResults();

        return $this->stats['errors'] > 0 ? 1 : 0;
    }

    /**
     * Get all chapter directories from the comic path
     */
    protected function getChapterDirectories(string $basePath): array
    {
        $dirs = [];
        $items = scandir($basePath);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $basePath . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                // Try to extract chapter number from directory name
                $chapterNumber = $this->extractChapterNumber($item);
                
                if ($chapterNumber !== null) {
                    $dirs[] = [
                        'name' => $item,
                        'path' => $fullPath,
                        'chapter_number' => $chapterNumber,
                    ];
                }
            }
        }

        // Sort by chapter number
        usort($dirs, fn($a, $b) => $a['chapter_number'] <=> $b['chapter_number']);

        return $dirs;
    }

    /**
     * Extract chapter number from directory name
     */
    protected function extractChapterNumber(string $dirName): ?int
    {
        // Try patterns like "Chapter 1", "Chapter 1", "1", etc.
        if (preg_match('/(\d+)/', $dirName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Process a single chapter directory
     */
    protected function processChapterDirectory(array $chapterDir, Comic $comic, bool $skipExisting): void
    {
        $chapterNumber = $chapterDir['chapter_number'];
        $chapterDirName = $chapterDir['name'];

        // Check if chapter already exists
        if ($this->chapterUploadService->chapterExists($comic->id, $chapterNumber)) {
            if ($skipExisting) {
                $this->stats['chapters_skipped']++;
                $this->line("  Skipping existing chapter {$chapterNumber}");
                return;
            }

            $this->line("  Replacing existing chapter {$chapterNumber}");
        }

        // Get images in chapter directory
        $imageFiles = $this->getImageFiles($chapterDir['path']);
        
        if (empty($imageFiles)) {
            $this->warn("  No valid images found in {$chapterDirName}");
            return;
        }

        // Convert files to UploadedFile instances
        $uploadedFiles = [];
        foreach ($imageFiles as $imageFile) {
            try {
                $uploadedFiles[] = $this->chapterUploadService->createUploadedFile($imageFile['path']);
            } catch (\Exception $e) {
                $this->stats['images_skipped']++;
                $this->error("    Failed to create UploadedFile for {$imageFile['name']}: {$e->getMessage()}");
            }
        }

        if (empty($uploadedFiles)) {
            $this->warn("  No valid uploaded files created for {$chapterDirName}");
            return;
        }

        // Use service to upload chapter
        try {
            $this->chapterUploadService->upload($comic->id, $uploadedFiles);
            
            $this->stats['chapters_uploaded']++;
            $this->stats['images_uploaded'] += count($uploadedFiles);
            $this->line("  Uploaded chapter {$chapterNumber} with " . count($uploadedFiles) . " images");
            
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("  Failed to upload chapter {$chapterNumber}: {$e->getMessage()}");
        }
    }

    /**
     * Get all valid image files from a directory
     */
    protected function getImageFiles(string $directory): array
    {
        $files = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $item;
            if (is_file($fullPath)) {
                $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                
                if (in_array($extension, $allowedExtensions)) {
                    // Extract page number from filename
                    $pageNumber = $this->extractPageNumber($item);
                    
                    $files[] = [
                        'name' => $item,
                        'path' => $fullPath,
                        'page_number' => $pageNumber,
                        'extension' => $extension,
                    ];
                }
            }
        }

        // Sort by page number naturally
        usort($files, fn($a, $b) => $a['page_number'] <=> $b['page_number']);

        return $files;
    }

    /**
     * Extract page number from filename
     */
    protected function extractPageNumber(string $filename): int
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Extract number using regex
        if (preg_match('/(\d+)/', $name, $matches)) {
            return (int) $matches[1];
        }

        // Fallback: use hash of filename
        return crc32($name);
    }

    /**
     * Display upload results
     */
    protected function displayResults(): void
    {
        $this->newLine();
        $this->info('=== Upload Results ===');
        $this->info("Chapters found: {$this->stats['chapters_found']}");
        $this->info("Chapters uploaded: {$this->stats['chapters_uploaded']}");
        $this->info("Chapters skipped: {$this->stats['chapters_skipped']}");
        $this->info("Images uploaded: {$this->stats['images_uploaded']}");
        $this->info("Images skipped: {$this->stats['images_skipped']}");
        
        if ($this->stats['errors'] > 0) {
            $this->error("Errors encountered: {$this->stats['errors']}");
        } else {
            $this->info('✓ Upload completed successfully!');
        }
    }
}
