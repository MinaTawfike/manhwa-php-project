<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class Comic extends Model
{
    protected $fillable = ['title', 'description', 'poster', 'status', 'latest_update', 'options'];
    
    protected $casts = [
        'options' => 'array',
        'latest_update' => 'datetime',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comic) {
            $comic->slug = static::generateUniqueSlug($comic->title);
        });

        static::updating(function ($comic) {
            if ($comic->isDirty('title')) {
                $comic->slug = static::generateUniqueSlug($comic->title, $comic->id);
            }
        });
    }

    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
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
