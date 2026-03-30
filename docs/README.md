# 🎌 Manhwa Project - Laravel Application

A complete Laravel-based manhwa (comic) reading platform with chapter management, user interactions, and modern dark theme UI.

**Status**: ✅ **Production Ready**  
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

## 📚 Documentation

- **[Quick Start Guide](./QUICK_START.md)** - 5-minute setup guide
- **[Build Documentation](./BUILD_COMPLETE.md)** - Technical implementation details
- **[Project Status](./PROJECT_STATUS.md)** - Complete feature overview
- **[Conversion Guide](./LARAVEL_CONVERSION_GUIDE.md)** - Django to Laravel conversion process

---

## 📝 License

Open source project available for enhancement and customization.

---

**Last Updated**: January 2026  
**Version**: 1.0  
**Status**: ✅ Production Ready

🎌 **Happy Coding!** 🎌
