<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\ChapterImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterUploadService
{
    /**
     * Upload a chapter with images using the exact same logic as controller
     */
    public function upload(int $comicId, array $images): Chapter
    {
        return DB::transaction(function () use ($comicId, $images) {
            // Get next chapter number
            $chapterNumber = $this->getNextChapterNumber($comicId);
            
            // Create chapter using same logic as controller
            $chapter = Chapter::create([
                'name' => null,
                'number' => $chapterNumber,
                'comic_id' => $comicId,
                'user_id' => auth()->id() ?? 1, // Fallback for console commands
            ]);

            // Handle images using the exact same logic as controller
            $page = 1;
            foreach ($images as $image) {
                $path = $this->uploadImage($image, $chapter);
                
                ChapterImage::create([
                    'chapter_id' => $chapter->id,
                    'path' => $path,
                    'page_number' => $page++,
                    'alt' => null,
                ]);
            }

            return $chapter;
        });
    }

    /**
     * Upload image using the exact same logic as controller
     */
    private function uploadImage($image, $chapter): string
    {
        // This is the exact same method from ChapterController
        $path = $image->storeAs("comics/{$chapter->comic->id}/chapter-id-{$chapter->id}", $image->hashName(), 'r2');
        return Storage::disk('r2')->url($path);
    }

    /**
     * Convert local file to UploadedFile instance
     */
    public function createUploadedFile(string $filePath): UploadedFile
    {
        $originalName = basename($filePath);
        $mimeType = mime_content_type($filePath);
        $size = filesize($filePath);
        $error = null;

        return new UploadedFile(
            $filePath,
            $originalName,
            $mimeType,
            $error,
            true
        );
    }

    /**
     * Get next chapter number for a comic
     */
    public function getNextChapterNumber(int $comicId): int
    {
        $lastChapter = Chapter::where('comic_id', $comicId)
            ->orderBy('number', 'desc')
            ->first();

        return $lastChapter ? $lastChapter->number + 1 : 1;
    }

    /**
     * Check if chapter already exists
     */
    public function chapterExists(int $comicId, int $chapterNumber): bool
    {
        return Chapter::where('comic_id', $comicId)
            ->where('number', $chapterNumber)
            ->exists();
    }
}
