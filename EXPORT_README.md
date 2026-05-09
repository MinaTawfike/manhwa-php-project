# Comic Export Command

## Overview
This Laravel command generates clean SQL export files for specific comic chapters and their images, designed for safe import into InfinityFree phpMyAdmin.

## Command Usage

```bash
php artisan export:comic --comic_id=4 --from=4 --to=47
```

### Parameters
- `--comic_id`: Comic ID to export (required)
- `--from`: Starting chapter number (required)  
- `--to`: Ending chapter number (required)

### Example
```bash
php artisan export:comic --comic_id=4 --from=4 --to=47
```

## Output
The command generates a SQL file at:
```
storage/app/exports/comic_4_chapters_4_47.sql
```

## Features

### 1. Clean Export
- Exports ONLY specified chapters and their images
- No CREATE TABLE statements
- No AUTO_INCREMENT resets
- Safe for InfinityFree import

### 2. Data Integrity
- Proper string escaping
- NULL value handling
- Foreign key management
- Duplicate prevention

### 3. Cleanup (Recommended)
The generated SQL includes cleanup statements to prevent conflicts:
```sql
DELETE FROM `chapter_images` WHERE `chapter_id` IN (...);
DELETE FROM `chapters` WHERE `comic_id` = 4 AND `number` BETWEEN 4 AND 47;
```

### 4. Verification
Includes verification queries to confirm successful import:
```sql
SELECT COUNT(*) as chapters_exported FROM chapters WHERE comic_id = 4 AND number BETWEEN 4 AND 47;
SELECT COUNT(*) as images_exported FROM chapter_images WHERE chapter_id IN (...);
```

## SQL Structure

### Chapters Table
```sql
INSERT INTO `chapters` (`id`, `comic_id`, `user_id`, `name`, `number`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(id, comic_id, user_id, name, number, rating, comment, created_at, updated_at);
```

### Chapter Images Table
```sql
INSERT INTO `chapter_images` (`id`, `chapter_id`, `path`, `page_number`, `alt`, `created_at`, `updated_at`) VALUES
(id, chapter_id, path, page_number, alt, created_at, updated_at);
```

## Import Instructions

### 1. Backup InfinityFree Database
Always backup before importing.

### 2. Import via phpMyAdmin
1. Open phpMyAdmin on InfinityFree
2. Select your database
3. Click "Import" tab
4. Choose the generated SQL file
5. Click "Go"

### 3. Verify Import
Run the verification queries included at the bottom of the SQL file.

## Error Handling

### Common Issues
1. **Foreign Key Constraints**: Handled automatically with `SET FOREIGN_KEY_CHECKS=0/1`
2. **Duplicate Entries**: Cleanup statements prevent conflicts
3. **Missing Data**: Verification queries help identify issues

### Troubleshooting
- Check that chapters exist in the specified range
- Verify comic_id is correct
- Ensure file permissions on storage/app/exports/

## File Locations

- **Command**: `app/Console/Commands/ExportComicCommand.php`
- **Output Directory**: `storage/app/exports/`
- **Test Script**: `test_export.php`

## Requirements

- Laravel 8+ / 9+ / 10+
- MySQL 5.7+ / 8.0+
- Proper database connections configured

## Security Notes

- SQL files contain only the specified data
- No sensitive information exposed
- Safe for production environments
- String escaping prevents SQL injection
