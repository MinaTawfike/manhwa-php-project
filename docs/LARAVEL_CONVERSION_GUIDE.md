# Django to Laravel Conversion Guide - Manhwa Project

## Overview
Complete step-by-step guide to rebuild the Django-based Manhwa application in Laravel, maintaining the same **functionality, style, and logic**. 

**⚠️ IMPORTANT**: This is a **fresh start** - no data migration needed. Focus is on recreating the architecture, features, and user experience in Laravel.

**Stack:** Laravel 11, PostgreSQL, Cloudinary for media storage

### Key Points
✅ **Fresh Database** - Start with clean schema, no data import  
✅ **Same Features** - Comic management, chapters, pages, ratings, bookmarks, comments  
✅ **Same UI/UX** - Grid layouts, chapter reader, user interactions  
✅ **Same Logic** - Bookmark toggles, rating system, comment storage  
✅ **No Data Export** - All database tables created from scratch

---

## Phase 1: Setup & Environment

### Step 1.1: Create New Laravel Project
```bash
# Create a new Laravel project
composer create-project laravel/laravel manhwa-app --prefer-dist

cd manhwa-app
```

### Step 1.2: Configure Environment
```bash
# Copy .env template
cp .env.example .env

# Generate app key
php artisan key:generate
```

### Step 1.3: Update .env Configuration
```env
APP_NAME="Manhwa Website"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql  # or mysql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=manhwa_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Cloudinary Configuration (from requirements)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Step 1.4: Install Required Packages
```bash
composer require cloudinary/cloudinary_php
composer require laravel/socialite  # If user auth needed
composer require spatie/laravel-honeypot  # For admin honeypot like Django
```

---

## Phase 2: Database Setup (Models & Migrations)

### Step 2.1: Create Models with Migrations
```bash
# Create models with migration and controller
php artisan make:model Comic -m -c
php artisan make:model Chapter -m -c
php artisan make:model Page -m -c
```

### Step 2.2: Create Comic Migration
**File: `database/migrations/XXXX_XX_XX_create_comics_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('poster')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'hiatus'])->default('ongoing');
            $table->timestamp('latest_update')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
```

### Step 2.3: Create Chapter Migration
**File: `database/migrations/XXXX_XX_XX_create_chapters_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comic_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->integer('number');
            $table->integer('rating')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Pivot table for bookmarks
        Schema::create('chapter_user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['chapter_id', 'user_id']);
        });

        // Pivot table for ratings by users
        Schema::create('chapter_user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->default(0);
            $table->timestamps();
            $table->unique(['chapter_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_user_ratings');
        Schema::dropIfExists('chapter_user_bookmarks');
        Schema::dropIfExists('chapters');
    }
};
```

### Step 2.4: Create Page Migration
**File: `database/migrations/XXXX_XX_XX_create_pages_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->integer('page_number');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
```

### Step 2.5: Run Migrations
```bash
php artisan migrate
```

---

## Phase 3: Create Eloquent Models

### Step 3.1: Comic Model
**File: `app/Models/Comic.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
```

### Step 3.2: Chapter Model
**File: `app/Models/Chapter.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chapter extends Model
{
    protected $fillable = ['comic_id', 'name', 'number', 'rating', 'comment'];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
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
}
```

### Step 3.3: Page Model
**File: `app/Models/Page.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $fillable = ['chapter_id', 'page_number', 'image'];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
```

### Step 3.4: Update User Model
**File: `app/Models/User.php`**

Add these relationships to the existing User model:

```php
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
```

---

## Phase 4: Create Controllers

### Step 4.1: Comic Controller
**File: `app/Http/Controllers/ComicController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\View\View;

class ComicController extends Controller
{
    public function index(): View
    {
        $comics = Comic::with('chapters')->paginate(20);
        return view('comics.index', compact('comics'));
    }

    public function show(Comic $comic): View
    {
        $comic->load('chapters.pages');
        return view('comics.show', compact('comic'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image',
            'status' => 'required|in:ongoing,completed,hiatus',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->uploadImage($request->file('poster'));
        }

        Comic::create($validated);
        return redirect()->route('comics.index')->with('success', 'Comic created successfully');
    }

    private function uploadImage($image)
    {
        // Use Cloudinary
        $uploaded = cloudinary()->upload($image->getRealPath());
        return $uploaded->getSecurePath();
    }
}
```

### Step 4.2: Chapter Controller
**File: `app/Http/Controllers/ChapterController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChapterController extends Controller
{
    public function show(Comic $comic, Chapter $chapter): View
    {
        $chapter->load('pages', 'bookmarkedBy', 'ratedBy');
        return view('chapters.show', compact('comic', 'chapter'));
    }

    public function bookmark(Chapter $chapter): RedirectResponse
    {
        auth()->user()->bookmarkedChapters()->toggle($chapter->id);
        return back()->with('success', 'Bookmark toggled');
    }

    public function rate(Chapter $chapter, int $rating): RedirectResponse
    {
        auth()->user()->ratedChapters()->updateExistingPivot(
            $chapter->id,
            ['rating' => $rating]
        );
        return back()->with('success', 'Rating saved');
    }

    public function comment(Chapter $chapter, string $comment): RedirectResponse
    {
        $chapter->update(['comment' => $comment]);
        return back()->with('success', 'Comment saved');
    }
}
```

---

## Phase 5: Create Routes

### Step 5.1: Web Routes
**File: `routes/web.php`**

```php
<?php

use App\Http\Controllers\ComicController;
use App\Http\Controllers\ChapterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComicController::class, 'index'])->name('comics.index');
Route::get('/comics/{comic}', [ComicController::class, 'show'])->name('comics.show');

Route::middleware('auth')->group(function () {
    Route::post('/comics', [ComicController::class, 'store'])->name('comics.store');
    Route::get('/comics/{comic}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::post('/chapters/{chapter}/bookmark', [ChapterController::class, 'bookmark'])->name('chapters.bookmark');
    Route::post('/chapters/{chapter}/rate/{rating}', [ChapterController::class, 'rate'])->name('chapters.rate');
    Route::post('/chapters/{chapter}/comment', [ChapterController::class, 'comment'])->name('chapters.comment');
});

require __DIR__.'/auth.php';
```

---

## Phase 6: Create Views (Blade Templates)

### Step 6.1: Comics Index View
**File: `resources/views/comics/index.blade.php`**

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manhwa Comics</h1>
    
    <div class="grid grid-cols-4 gap-4">
        @foreach($comics as $comic)
            <div class="card">
                <img src="{{ $comic->poster }}" alt="{{ $comic->title }}">
                <h3>{{ $comic->title }}</h3>
                <p class="status-{{ $comic->status }}">{{ ucfirst($comic->status) }}</p>
                <p>{{ Str::limit($comic->description, 100) }}</p>
                <a href="{{ route('comics.show', $comic) }}" class="btn btn-primary">View</a>
            </div>
        @endforeach
    </div>

    {{ $comics->links() }}
</div>
@endsection
```

### Step 6.2: Comic Detail View
**File: `resources/views/comics/show.blade.php`**

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="comic-header">
        <img src="{{ $comic->poster }}" alt="{{ $comic->title }}" class="poster">
        <div class="details">
            <h1>{{ $comic->title }}</h1>
            <p class="status">Status: {{ ucfirst($comic->status) }}</p>
            <p class="updated">Last Update: {{ $comic->latest_update?->diffForHumans() }}</p>
            <p class="description">{{ $comic->description }}</p>
        </div>
    </div>

    <h2>Chapters</h2>
    <div class="chapters-list">
        @foreach($comic->chapters()->orderBy('number', 'desc')->get() as $chapter)
            <div class="chapter-item">
                <h3>Chapter {{ $chapter->number }} 
                    @if($chapter->name) - {{ $chapter->name }} @endif
                </h3>
                <p>Rating: {{ $chapter->rating }}/10</p>
                @if($chapter->comment)
                    <p>{{ $chapter->comment }}</p>
                @endif
                <a href="{{ route('chapters.show', [$comic, $chapter]) }}" class="btn">Read</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
```

### Step 6.3: Chapter Reader View
**File: `resources/views/chapters/show.blade.php`**

```blade
@extends('layouts.app')

@section('content')
<div class="chapter-reader">
    <div class="chapter-header">
        <h1>{{ $comic->title }} - Chapter {{ $chapter->number }}</h1>
        <div class="actions">
            @auth
                <form action="{{ route('chapters.bookmark', $chapter) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-bookmark">
                        {{ auth()->user()->bookmarkedChapters()->where('chapter_id', $chapter->id)->exists() ? 'Unbookmark' : 'Bookmark' }}
                    </button>
                </form>

                <div class="rating">
                    @for($i = 1; $i <= 10; $i++)
                        <form action="{{ route('chapters.rate', [$chapter, $i]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit">{{ $i }}</button>
                        </form>
                    @endfor
                </div>
            @endauth
        </div>
    </div>

    <div class="pages-container">
        @foreach($chapter->pages()->orderBy('page_number')->get() as $page)
            <div class="page">
                <img src="{{ $page->image }}" alt="Page {{ $page->page_number }}">
            </div>
        @endforeach
    </div>

    @auth
        <div class="comment-section">
            <h3>Comment</h3>
            <form action="{{ route('chapters.comment', $chapter) }}" method="POST">
                @csrf
                <textarea name="comment" rows="4" placeholder="Add a comment...">{{ $chapter->comment }}</textarea>
                <button type="submit" class="btn btn-primary">Save Comment</button>
            </form>
        </div>
    @endauth
</div>
@endsection
```

---

## Phase 7: Admin Setup (Using Honeypot)

### Step 7.1: Create Admin Routes
**File: `routes/admin.php`**

```php
<?php

use App\Http\Controllers\Admin\ComicAdminController;
use App\Http\Controllers\Admin\ChapterAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('comics', ComicAdminController::class);
    Route::resource('chapters', ChapterAdminController::class);
});
```

### Step 7.2: Add Honeypot Routes
**File: `routes/web.php`** (add)

```php
// Honeypot fake admin route (for security)
Route::get('/administrator', function () {
    return redirect('/');
});

Route::post('/administrator/login', function () {
    return redirect('/');
});
```

---

## Phase 8: Image Storage Configuration

### Step 8.1: Setup Cloudinary
**File: `config/cloudinary.php`** (create)

```php
<?php

return [
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],
];
```

### Step 8.2: Create Image Service
**File: `app/Services/ImageUploadService.php`**

```php
<?php

namespace App\Services;

use Cloudinary\Cloudinary;

class ImageUploadService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => config('cloudinary.cloud'),
        ]);
    }

    public function upload($file, $folder = 'manhwa')
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            ['folder' => $folder]
        );

        return $result['secure_url'];
    }
}
```

---

## Phase 9: Authentication & Authorization

### Step 9.1: Create Admin Middleware
**File: `app/Http/Middleware/IsAdmin.php`**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        return $next($request);
    }
}
```

### Step 9.2: Add is_admin Column to Users
**File: `database/migrations/XXXX_XX_XX_add_is_admin_to_users_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
```

---

## Phase 10: Seeding Sample Data

### Step 10.1: Create Seeders
```bash
php artisan make:seeder ComicSeeder
php artisan make:seeder ChapterSeeder
php artisan make:seeder PageSeeder
```

### Step 10.2: Comic Seeder
**File: `database/seeders/ComicSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\Comic;
use Illuminate\Database\Seeder;

class ComicSeeder extends Seeder
{
    public function run(): void
    {
        Comic::create([
            'title' => 'Solo Leveling',
            'description' => 'A man with the weakest ability gets the chance to grow stronger',
            'status' => 'completed',
            'latest_update' => now(),
        ]);

        Comic::create([
            'title' => 'Tower of God',
            'description' => 'A boy climbs a mysterious tower',
            'status' => 'ongoing',
            'latest_update' => now(),
        ]);
    }
}
```

### Step 10.3: Run Seeders
```bash
php artisan db:seed
```

---

## Phase 11: Testing Setup

### Step 11.1: Create Feature Tests
**File: `tests/Feature/ComicTest.php`**

```php
<?php

namespace Tests\Feature;

use App\Models\Comic;
use App\Models\User;
use Tests\TestCase;

class ComicTest extends TestCase
{
    public function test_can_list_comics(): void
    {
        Comic::factory()->count(5)->create();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewHas('comics');
    }

    public function test_can_view_comic(): void
    {
        $comic = Comic::factory()->create();

        $response = $this->get(route('comics.show', $comic));
        $response->assertStatus(200);
        $response->assertViewHas('comic', $comic);
    }

    public function test_user_can_bookmark_chapter(): void
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create();

        $this->actingAs($user)
            ->post(route('chapters.bookmark', $chapter))
            ->assertRedirect();

        $this->assertTrue($user->bookmarkedChapters()->where('chapter_id', $chapter->id)->exists());
    }
}
```

---

## Phase 12: Environment & Deployment

### Step 12.1: Production .env Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=your_db_host
DB_DATABASE=manhwa_prod
DB_USERNAME=your_username
DB_PASSWORD=strong_password

CLOUDINARY_CLOUD_NAME=your_production_cloud
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Step 12.2: Deployment Checklist
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Migrate database
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Collect static assets
npm install
npm run build
```

---

## Phase 13: Frontend Styling (Blade + Tailwind)

### Step 13.1: Install Tailwind CSS
```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### Step 13.2: Configure Tailwind
**File: `tailwind.config.js`**

```js
export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1f2937',
        secondary: '#6366f1',
      },
    },
  },
  plugins: [],
}
```

---

## Phase 14: API Optional (If Needed)

### Step 14.1: Create API Routes
**File: `routes/api.php`**

```php
<?php

use App\Http\Controllers\Api\ComicController;
use App\Http\Controllers\Api\ChapterController;
use Illuminate\Support\Facades\Route;

Route::get('/comics', [ComicController::class, 'index']);
Route::get('/comics/{comic}', [ComicController::class, 'show']);
Route::get('/comics/{comic}/chapters/{chapter}', [ChapterController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chapters/{chapter}/bookmark', [ChapterController::class, 'bookmark']);
    Route::post('/chapters/{chapter}/rate', [ChapterController::class, 'rate']);
});
```

---

## Migration Mapping Summary

| Django | Laravel |
|--------|---------|
| `models.py` | `app/Models/` |
| `views.py` | `app/Http/Controllers/` |
| `urls.py` | `routes/` |
| `admin.py` | Admin controllers + Spatie Admin |
| Templates (HTML) | `resources/views/` (Blade) |
| `migrations/` | `database/migrations/` |
| `settings.py` | `config/` |
| Static files | `public/` |
| Media storage | Cloudinary integration |

---

## Key Differences to Remember

1. **ORM**: Django ORM → Eloquent ORM
2. **Views**: Django Views → Laravel Controllers
3. **Templates**: Django Templates → Blade Templates
4. **URLs**: Django URL patterns → Laravel Route groups
5. **Migrations**: Similar concept but Eloquent syntax
6. **Admin**: Django Admin → Custom Admin or Filament/Nova
7. **Middleware**: Similar authentication/authorization patterns
8. **Static Files**: Django static → Laravel public/
9. **Environment**: Similar .env configuration approach

---

## Verification Checklist

- [ ] Database migrations run successfully
- [ ] Models created with correct relationships
- [ ] Controllers handle all CRUD operations
- [ ] Routes defined for all functionality
- [ ] Blade templates render correctly
- [ ] Authentication middleware working
- [ ] Image uploads to Cloudinary successful
- [ ] Bookmarking functionality working
- [ ] Rating system functional
- [ ] Comments save correctly
- [ ] Admin panel accessible and functional
- [ ] Tests passing
- [ ] No database constraint violations

---

## Next Steps

1. Start with Phase 1-3 for basic setup
2. Test database migrations thoroughly
3. Build controllers and routes
4. Create and style views
5. Implement authentication
6. Test all functionality
7. Deploy to production

This Laravel application will have feature parity with the original Django version while leveraging Laravel's ecosystem benefits.
