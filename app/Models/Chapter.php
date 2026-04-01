<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;



class Chapter extends Model
{
    protected $fillable = ['comic_id', 'name', 'number', 'rating', 'comment', 'user_id'];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chapter_user_bookmarks')
            ->withTimestamps();
    }

    public function ratedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chapter_user_ratings')
            ->withPivot('rating')
            ->withTimestamps();
    }
    
    use HasFactory;

    // ... existing $fillable etc.

    /**
     * Images (pages) for this chapter, ordered by page_number.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ChapterImage::class)->orderBy('page_number');
    }

    protected static function booted()
    {
        static::deleting(function ($chapter) {

            // Load images if not loaded
            $chapter->loadMissing('images');


            // Delete chapter folder (cleanup)
            Storage::disk('r2')
               ->deleteDirectory("comics/{$chapter->comic->id}/chapter-id-{$chapter->id}");
        });
    }

}
