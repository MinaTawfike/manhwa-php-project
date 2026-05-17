<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ViewTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'view_tracking';

    /**
     * The attributes that are mass assignable.
     */

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'comic_id',
        'chapter_id',
        'user_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the comic that was viewed.
     */
    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    /**
     * Get the chapter that was viewed.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the user who viewed the content.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get views for comics.
     */
    public function scopeForComics($query)
    {
        return $query->whereNotNull('comic_id');
    }

    /**
     * Scope to get views for chapters.
     */
    public function scopeForChapters($query)
    {
        return $query->whereNotNull('chapter_id');
    }

    /**
     * Scope to get views within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent views.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get unique views (count unique users per comic).
     */
    public function scopeUniqueViews($query)
    {
        return $query->selectRaw('comic_id, COUNT(DISTINCT COALESCE(CAST(user_id AS CHAR), MD5(CONCAT(ip_address, user_agent)))) as unique_views')
            ->groupBy('comic_id');
    }

    /**
     * Get unique view count for a specific comic.
     * Counts unique visitors (logged-in users by user_id, guests by IP + user agent).
     * 
     * Logic:
     * - For logged-in users: Uses CAST(user_id AS CHAR) for reliable identification
     * - For guest users: Uses MD5(CONCAT(ip_address, user_agent)) for fingerprinting
     * - COALESCE ensures one identifier per visitor
     * - COUNT(DISTINCT ...) ensures each visitor counts once per comic
     * 
     * @param int $comicId The ID of the comic
     * @return int Number of unique visitors who viewed this comic
     */
    public static function getUniqueViewsForComic(int $comicId): int
    {
        return static::forComics()
            ->where('comic_id', $comicId)
            ->selectRaw('COUNT(DISTINCT COALESCE(CAST(user_id AS CHAR), MD5(CONCAT(ip_address, user_agent)))) as unique_count')
            ->value('unique_count') ?? 0;
    }

    /**
     * Get unique view count for a specific chapter.
     * Counts unique visitors (logged-in users by user_id, guests by IP + user agent).
     * 
     * Logic:
     * - For logged-in users: Uses CAST(user_id AS CHAR) for reliable identification
     * - For guest users: Uses MD5(CONCAT(ip_address, user_agent)) for fingerprinting
     * - COALESCE ensures one identifier per visitor
     * - COUNT(DISTINCT ...) ensures each visitor counts once per chapter
     * - Filtered by chapter_id to count only views of THIS SPECIFIC chapter
     * - Different chapters count separately even from the same visitor
     * 
     * @param int $chapterId The ID of the chapter
     * @return int Number of unique visitors who viewed this specific chapter
     */
    public static function getUniqueViewsForChapter(int $chapterId): int
    {
        return static::forChapters()
            ->where('chapter_id', $chapterId)
            ->selectRaw('COUNT(DISTINCT COALESCE(CAST(user_id AS CHAR), MD5(CONCAT(ip_address, user_agent)))) as unique_count')
            ->value('unique_count') ?? 0;
    }
}
