<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'role' => 'string',
    ];

    public function bookmarkedChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'chapter_user_bookmarks')
            ->withTimestamps();
    }

    public function ratedChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'chapter_user_ratings')
            ->withPivot('rating')
            ->withTimestamps();
    }

    public function bookmarkedComics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class, 'comic_user_bookmarks')
            ->withTimestamps();
    }

    public function lastChapterPerComic(): HasMany
    {
        return $this->hasMany(ComicUserLastChapter::class);
    }
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function unreadBookmarkedComicsCount(): int
    {
        $count = 0;

        // Load relations once (avoid N+1)
        $comics = $this->bookmarkedComics()
            ->with(['chapters', 'userLastChapters'])
            ->get();

        foreach ($comics as $comic) {

            // Latest chapter
            $latestChapter = $comic->chapters->sortByDesc('id')->first();

            if (! $latestChapter) {
                continue;
            }

            // User progress for this comic
            $progress = $comic->userLastChapters
                ->where('user_id', $this->id)
                ->first();

            // Never read
            if (! $progress) {
                $count++;
                continue;
            }

            // Has unread
            if ($latestChapter->id > $progress->chapter_id) {
                $count++;
            }
        }

        return $count;
    }

}
