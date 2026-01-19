# Manhwa Project - Project Map

## Overview
A Django-based web application for managing and displaying manhwa (Korean comics) content. The application provides features for viewing comics, managing chapters, user interactions (ratings, comments, bookmarks), and admin functionality.

**Stack:** Django 5.2, PostgreSQL (configured), Cloudinary for media storage, Gunicorn

---

## Directory Structure

```
project/
├── bin/                              # Main application directory
│   ├── manage.py                     # Django management script
│   ├── requirements.txt              # Python dependencies
│   ├── db.sqlite3                    # SQLite database (development)
│   │
│   ├── manhwa_website/               # Main Django project settings
│   │   ├── __init__.py
│   │   ├── settings.py               # Django configuration (DB, installed apps, middleware)
│   │   ├── urls.py                   # Main URL routing
│   │   ├── asgi.py                   # ASGI configuration (async)
│   │   └── wsgi.py                   # WSGI configuration (production)
│   │
│   ├── webpages/                     # Main Django app
│   │   ├── migrations/               # Database migrations
│   │   │   ├── 0001_initial.py
│   │   │   ├── 0002_alter_comic_poster_alter_comic_status.py
│   │   │   ├── 0003_page.py
│   │   │   ├── 0004_chapter_name.py
│   │   │   ├── 0005_alter_comic_options.py
│   │   │   ├── 0006_chapter_bookmarked_by.py
│   │   │   ├── 0007_alter_chapter_bookmarked_by.py
│   │   │   ├── 0008_chapter_comment_chapter_rating.py
│   │   │   ├── 0009_chapter_people_who_rated.py
│   │   │   ├── 0010_alter_comic_latestupdate.py
│   │   │   └── __init__.py
│   │   ├── __init__.py
│   │   ├── admin.py                  # Django admin configuration
│   │   ├── apps.py                   # App configuration
│   │   ├── models.py                 # Database models (Comic, Chapter, Page, User interactions)
│   │   ├── views.py                  # View logic for handling requests
│   │   ├── urls.py                   # App-level URL routing
│   │   ├── tests.py                  # Unit tests
│   │   ├── temps/                    # Temporary files directory
│   │   └── templates/                # HTML templates (inferred)
│   │
│   └── staticfiles/                  # Collected static files
│       └── admin/                    # Django admin static files (JS, CSS)
│
├── Lib/                              # Python virtual environment packages
│   └── site-packages/                # Installed dependencies
│
├── Scripts/                          # Virtual environment scripts (activate, etc.)
│
├── pyvenv.cfg                        # Virtual environment configuration
│
└── .gitignore                        # Git ignore rules

```

---

## Key Components

### 1. **Project Configuration** (`manhwa_website/`)
- **settings.py**: Django configuration including:
  - Installed apps (webpages, admin_honeypot, cloudinary_storage)
  - Database configuration (supports PostgreSQL via dj_database_url)
  - Static files and media handling (Cloudinary integration)
  - Security settings (CSRF, CORS)
  - Admin panel setup (with honeypot for fake admin paths)

- **urls.py**: Main URL dispatcher routing to webpages app

- **wsgi.py / asgi.py**: Server interfaces for deployment (Gunicorn)

### 2. **Main Application** (`webpages/`)

#### Models (Database Schema)
- **Comic**: Main manga/manhwa entries
  - Fields: title, description, poster, status, latest_update, options, created_at, updated_at
  - Relations: Can have multiple chapters

- **Chapter**: Individual chapters of comics
  - Fields: name, number, comic (ForeignKey), bookmarked_by (M2M with User), comment, rating, people_who_rated
  - Relations: Many chapters per comic, many pages per chapter

- **Page**: Individual pages within chapters
  - Fields: page_number, image, chapter (ForeignKey)

- **User Interactions**:
  - Bookmarks: Users can bookmark chapters
  - Ratings: Users can rate chapters
  - Comments: Comments on chapters

#### Views (`views.py`)
- Handles HTTP requests and renders responses
- Likely includes: listing comics, displaying chapters, chapter detail views, user actions

#### Admin Interface (`admin.py`)
- Admin panel configuration for managing Comic, Chapter, and Page models
- Honeypot admin at fake path for security

#### URLs (`urls.py`)
- App-level URL patterns for routing comic pages, chapters, etc.

### 3. **Database Migrations** (`migrations/`)
- **0001**: Initial schema (Comic model)
- **0002**: Alter Comic poster and status fields
- **0003**: Add Page model for individual pages
- **0004**: Add Chapter name field
- **0005**: Alter Comic options
- **0006**: Add Chapter bookmarked_by field (M2M with User)
- **0007**: Alter Chapter bookmarked_by relationship
- **0008**: Add comment and rating to Chapter
- **0009**: Add people_who_rated tracking to Chapter
- **0010**: Alter Comic latest_update field

---

## Dependencies

Key packages (from `requirements.txt`):
- **Django 5.2**: Web framework
- **dj-database-url**: Database URL parsing
- **django-environ**: Environment variable management
- **psycopg2**: PostgreSQL adapter
- **cloudinary**: Media storage
- **django-cloudinary-storage**: Django integration for Cloudinary
- **admin-honeypot**: Fake admin URL for security
- **gunicorn**: WSGI application server
- **pillow**: Image processing
- **requests**: HTTP library

---

## Development Workflow

1. **Local Development**: Uses SQLite database (db.sqlite3)
2. **Run Server**: `python manage.py runserver`
3. **Create Migrations**: `python manage.py makemigrations`
4. **Apply Migrations**: `python manage.py migrate`
5. **Production**: Uses PostgreSQL with Gunicorn + Cloudinary

---

## File Statistics
- **Total Python Lines**: ~741 (excluding migrations)
- **Main Modules**: 
  - Core project settings
  - Single main app (webpages)
  - Models, views, admin, and URL configuration

---

## Environment Configuration
Likely uses environment variables for:
- `DATABASE_URL`: PostgreSQL connection string
- `CLOUDINARY_URL`: Cloudinary API credentials
- `SECRET_KEY`: Django secret
- `DEBUG`: Debug mode toggle
- `ALLOWED_HOSTS`: Allowed host names

---

## Next Steps for Development
1. Review `models.py` for complete data schema
2. Check `views.py` for business logic
3. Add templates if not already present
4. Configure environment variables for deployment
5. Set up tests in `tests.py`
