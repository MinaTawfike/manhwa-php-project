# Laravel Manhwa Project - Quick Start Guide

## 🚀 Start the Application

### Step 1: Navigate to Project
```bash
cd manhwa-php-project
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

### Admin Features (Super Admin Only)
- Create new comics: Click "+ Add Comic" button
- Edit existing comics: Click "Edit" on comic cards
- Delete comics: Click "Delete" button (with confirmation)

### User Interactions
- Bookmark chapters for later reading
- Rate chapters (1-10 scale)
- Add comments to chapters

---

## 🛠️ Common Commands

### Database Operations
```bash
# Reset database
php artisan migrate:refresh

# Access database console
php artisan tinker
```

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

### Testing
```bash
# Run tests
php artisan test
```

---

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # ComicController, ChapterController
│   ├── Models/              # Comic, Chapter, Page, User
│   └── ...
├── resources/views/         # Blade templates
├── database/migrations/     # Database schema
└── routes/web.php          # Application routes
```

---

## 🔧 Configuration

### Environment Variables
Key settings in `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# Image storage (optional)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

---

## 📚 More Documentation

- **[Build Documentation](./BUILD_COMPLETE.md)** - Technical details
- **[Project Status](./PROJECT_STATUS.md)** - Feature overview
- **[Conversion Guide](./LARAVEL_CONVERSION_GUIDE.md)** - Development history

---

**Need Help?** Check the main [README.md](./README.md) for full project information.
