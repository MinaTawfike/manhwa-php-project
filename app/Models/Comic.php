<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
