<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterImage extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'chapter_id',
        'path',
        'page_number',
        'alt',
    ];

    /**
     * Parent chapter.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Helper: Public URL using the 'public' disk convention.
     * Note: path is stored relative to 'public' disk, e.g. 'chapter_pages/1/foo.jpg'
     */
    public function url(): string
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }
}