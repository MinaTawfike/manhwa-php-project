<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComicUserLastChapter extends Model
{
    protected $table = 'comic_user_last_chapters';
    protected $fillable = ['comic_id', 'user_id', 'chapter_id'];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
