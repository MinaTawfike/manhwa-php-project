<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class Comic extends Model
{
    protected $fillable = ['title', 'description', 'poster', 'status', 'latest_update', 'options', 'user_id'];
    
    protected $casts = [
        'options' => 'array',
        'latest_update' => 'datetime',
        'views_count' => 'integer',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comic_user_bookmarks')
            ->withTimestamps();
    }

    public function userLastChapters(): HasMany
    {
        return $this->hasMany(ComicUserLastChapter::class);
    }

    public function latestChapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'latest_chapter_id');
    }

    public function viewTrackings(): HasMany
    {
        return $this->hasMany(ViewTracking::class);
    }

    /**
     * The categories that belong to this comic.
     * 
     * Many-to-many relationship through category_comic pivot table.
     * Uses eager loading to prevent N+1 queries.
     * 
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_comic')
            ->withTimestamps();
    }

    public function getLatestUpdateAttribute()
    {
        // Check if the latest_update attribute exists and is not null
        if (isset($this->attributes['latest_update']) && $this->attributes['latest_update']) {
            return $this->attributes['latest_update'];
        }
        
        // If latest_update is null, get the most recent chapter's created_at
        $latestChapterCreatedAt = $this->chapters()->max('created_at');
        
        // Convert string to Carbon object if it's not null
        if ($latestChapterCreatedAt) {
            return \Carbon\Carbon::parse($latestChapterCreatedAt);
        }
        
        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comic) {
            // Always generate slug for new comics
            $comic->slug = static::generateUniqueSlug($comic->title);
        });

        static::updating(function ($comic) {
            // Update slug if title changes or if slug is empty
            if ($comic->isDirty('title') || empty($comic->slug)) {
                $comic->slug = static::generateUniqueSlug($comic->title, $comic->id);
            }
        });
    }

    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
        // Handle empty/invalid titles
        if (empty($title) || trim($title) === '') {
            $title = 'comic-' . uniqid();
        }
        
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {

            $slug = $original . '-' . $count++;
        }

        return $slug;
    }


    protected static function booted()
    {
        static::deleting(function ($comic) {
            // Delete poster file if exists
            if ($comic->poster) {
                Storage::disk('r2')->delete($comic->poster);
                Storage::disk('r2')->deleteDirectory("comics/{$comic->id}");
            }
        });
    }
}
