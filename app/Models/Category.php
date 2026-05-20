<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Category Model
 * 
 * Represents a category/genre for organizing comics.
 * 
 * Features:
 * - Many-to-many relationship with Comics
 * - Automatic slug generation
 * - SEO-friendly URLs
 * - Eager loading support
 * 
 * @package App\Models
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The comics that belong to this category.
     * 
     * Many-to-many relationship through category_comic pivot table.
     * Uses eager loading to prevent N+1 queries.
     * 
     * @return BelongsToMany
     */
    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class, 'category_comic')
            ->withTimestamps()
            ->with('chapters'); // Eager load chapters for performance
    }

    /**
     * Boot method to handle model events.
     * 
     * Automatically generates slugs when creating or updating categories.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Always generate slug for new categories
            $category->slug = static::generateUniqueSlug($category->name);
        });

        static::updating(function ($category) {
            // Update slug if name changes or if slug is empty
            if ($category->isDirty('name') || empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    /**
     * Generate a unique slug for the category.
     * 
     * @param string $name The category name
     * @param int|null $ignoreId ID to ignore when checking uniqueness (for updates)
     * @return string Unique slug
     */
    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        // Handle empty/invalid names
        if (empty($name) || trim($name) === '') {
            $name = 'category-' . uniqid();
        }
        
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        // Ensure slug is unique
        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Scope to get categories with comic counts.
     * 
     * Useful for admin dashboard to show how many comics are in each category.
     * 
     * @param mixed $query
     * @return mixed
     */
    public function scopeWithComicCount($query)
    {
        return $query->withCount('comics');
    }

    /**
     * Get the URL for this category.
     * 
     * SEO-friendly URL using slug.
     * 
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }
}
