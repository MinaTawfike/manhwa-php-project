# 🎌 Manhwa Project - Laravel Application

A complete Laravel-based manhwa (comic) reading platform with chapter management, user interactions, and modern dark theme UI.

**Framework**: Laravel 12  
**Database**: SQLite (development) / PostgreSQL (production)  
**Theme**: Dark mode with red accents

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Composer
- SQLite or PostgreSQL

### Installation
```bash
# Clone and setup
git clone <repository-url>
cd manhwa-php-project
composer install
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Start server
php artisan serve
```

Visit `http://localhost:8000` to access the application.

---

## ✨ Features

### Core Features
- **Comic Management**: Browse, create, edit, and delete comics
- **Chapter System**: Read chapters with pagination
- **User Interactions**: Bookmark chapters, rate content (1-10), add comments
- **Image Handling**: Local storage with Cloudinary integration ready
- **Responsive Design**: Mobile-friendly dark theme UI

### Technical Features
- **Laravel 12**: Latest framework with modern features
- **Eloquent ORM**: Clean database relationships
- **Blade Templates**: Efficient server-side rendering
- **Form Validation**: Robust input validation and error handling
- **Security**: CSRF protection, input sanitization
- **Authentication Ready**: Breeze integration available

---

## 📚 Using the Application

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

### Trusted Proxies

If your app runs behind a proxy (Cloudflare, load balancer), set the `TRUSTED_PROXIES` environment variable to a comma-separated list of trusted proxy IPs or CIDRs. Use `*` to trust all proxies only if you understand the security implications.

Example:

```env
TRUSTED_PROXIES=203.0.113.1,198.51.100.0/24
```

This ensures `Request::ip()` and forwarded headers are interpreted correctly by the application.

---

## 🚀 Production Deployment

### Prerequisites
- PostgreSQL database (recommended)
- Web server (Nginx/Apache)
- SSL certificate

### Deployment Steps
1. Set up production database
2. Configure `.env` with production credentials
3. Run migrations: `php artisan migrate --force`
4. Set up Cloudinary for image uploads
5. Enable proper authentication (Breeze)
6. Optimize for production:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

---

## 📝 License

Open source project available for enhancement and customization.

---

**Last Updated**: January 2026  
**Version**: 1.0

🎌 **Happy Coding!** 🎌
