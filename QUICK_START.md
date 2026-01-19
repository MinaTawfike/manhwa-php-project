# Laravel Manhwa Project - Quick Start Guide

## �� Start the Application

### Step 1: Navigate to Project
```bash
cd /mnt/d/VS\ Code\ Projects/manhwa-project.worktrees/copilot-worktree-2026-01-17T16-25-44/manhwa-php-project
```

### Step 2: Install Dependencies (if needed)
```bash
composer install
```

### Step 3: Start Laravel Development Server
```bash
php artisan serve
```

The app will be available at: **http://localhost:8000**

---

## 📖 Using the Application

### Browse Comics
- Visit http://localhost:8000 to see all comics
- Click on any comic card to view details
- Scroll through chapters list

### Create Comics (Currently Testing)
Note: Authentication is currently disabled for testing. To enable:

```bash
php artisan breeze:install blade
php artisan migrate
npm install
npm run dev
```

Then:
1. Register a new user
2. Click "Add Comic" button
3. Fill in comic details
4. Click "Create Comic"

### View Chapters
1. Click "Read" button on any chapter
2. See all pages displayed
3. Scroll through pages
4. Rate the chapter (1-10)
5. Add a comment
6. Bookmark the chapter

---

## 🎨 Customization

### Change Theme Colors
Edit `resources/views/layouts/app.blade.php` and modify:
- `#ff6b6b` - Primary red color
- `#1a1a1a` - Dark background
- `#2a2a2a` - Card background

### Add Cloudinary
1. Update `.env`:
```env
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

2. Update `ComicController.php` `uploadImage()` method to use Cloudinary

### Enable User Profiles
Uncomment `require __DIR__.'/auth.php';` in `routes/web.php` after installing Breeze.

---

## 📁 Important Files

**Controllers:**
- `app/Http/Controllers/ComicController.php`
- `app/Http/Controllers/ChapterController.php`

**Models:**
- `app/Models/Comic.php`
- `app/Models/Chapter.php`
- `app/Models/Page.php`
- `app/Models/User.php`

**Routes:**
- `routes/web.php`

**Views:**
- `resources/views/layouts/app.blade.php` (main layout)
- `resources/views/comics/` (comic pages)
- `resources/views/chapters/` (chapter pages)

---

## 🔧 Database

Database is SQLite at: `database/database.sqlite`

To reset database:
```bash
php artisan migrate:refresh
```

To seed sample data (if you create seeders):
```bash
php artisan db:seed
```

---

## 📦 Available Routes

### Public Routes
- `GET /` - View all comics
- `GET /comics/{id}` - View comic details

### Authenticated Routes
- `GET /comics/create` - Create comic form
- `POST /comics` - Store comic
- `GET /comics/{id}/edit` - Edit comic form
- `PUT /comics/{id}` - Update comic
- `DELETE /comics/{id}` - Delete comic

- `GET /comics/{comic_id}/chapters/create` - Create chapter form
- `POST /comics/{comic_id}/chapters` - Store chapter
- `GET /comics/{comic_id}/chapters/{chapter_id}` - View chapter (reader)
- `POST /chapters/{id}/bookmark` - Toggle bookmark
- `POST /chapters/{id}/rate` - Rate chapter
- `POST /chapters/{id}/comment` - Comment on chapter

---

## ⚡ Performance Tips

1. **Pagination**: Comics are paginated (20 per page)
2. **Eager Loading**: Controllers use `with()` to prevent N+1 queries
3. **Caching**: Consider adding caching for comic listings
4. **Compression**: Enable gzip in web server for production

---

## 🐛 Troubleshooting

**Routes not working?**
```bash
php artisan route:clear
php artisan cache:clear
```

**Images not uploading?**
```bash
php artisan storage:link
```

**Database errors?**
```bash
php artisan migrate:refresh
```

**Composer errors?**
```bash
composer update
composer install
```

---

## 📝 Next Features to Add

- [ ] User authentication (Breeze)
- [ ] Cloudinary image uploads
- [ ] Admin dashboard
- [ ] Search functionality
- [ ] Tags/Categories
- [ ] User profiles
- [ ] Follow users
- [ ] Recommendations
- [ ] API endpoints
- [ ] Dark/Light theme toggle

---

## 🎯 Comparison with Django Version

| Feature | Django | Laravel |
|---------|--------|---------|
| Comics listing | ✅ | ✅ |
| Chapter management | ✅ | ✅ |
| Page viewer | ✅ | ✅ |
| Bookmarks | ✅ | ✅ |
| Ratings | ✅ | ✅ |
| Comments | ✅ | ✅ |
| Admin panel | ✅ | 🔄 Custom |
| Dark theme | ✅ | ✅ |
| Responsive | ✅ | ✅ |

---

## 💡 Support

For questions or issues:
1. Check `BUILD_COMPLETE.md` for detailed documentation
2. Check `LARAVEL_CONVERSION_GUIDE.md` for conversion details
3. Review controller code for logic
4. Check Blade templates for UI structure

**Happy coding! 🎌**
