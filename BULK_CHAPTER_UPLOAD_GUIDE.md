# Bulk Chapter Upload Implementation

## Overview
This implementation extracts the existing upload logic into a reusable service and creates a bulk upload command that uses the exact same logic.

## Files Created/Modified

### 1. Service Class
**Location**: `app/Services/ChapterUploadService.php`
- Extracts upload logic from ChapterController
- Maintains exact same behavior (R2 upload, random filenames, folder structure)
- Includes helper methods for UploadedFile creation

### 2. Updated Controller
**Location**: `app/Http/Controllers/ChapterController.php`
- Added service injection
- Modified `store()` method to use service
- Preserves all existing functionality

### 3. Artisan Command
**Location**: `app/Console/Commands/BulkUploadChapters.php`
- Uses the ChapterUploadService
- Reads from comic-specific directory structure
- Handles errors gracefully per chapter

## Directory Structure

### Expected Structure
```
storage/app/bulk_chapters/
├── 1/                    # Comic ID folder
│   ├── Chapter 1/
│   │   ├── 1.jpg
│   │   ├── 2.jpg
│   │   └── 10.jpg
│   └── Chapter 2/
│       ├── 1.jpg
│       └── 2.jpg
├── 2/                    # Another comic
│   └── Chapter 1/
│       └── 1.jpg
```

### Alternative Naming
The command also supports these directory naming patterns:
- `Chapter 1`, `Chapter 2`
- `1`, `2`, `3`
- Any name containing numbers

## Command Usage

### Basic Upload
```bash
php artisan chapters:bulk-upload --comic_id=1
```

### Skip Existing Chapters
```bash
php artisan chapters:bulk-upload --comic_id=1 --skip-existing
```

### Custom Path
```bash
php artisan chapters:bulk-upload --comic_id=1 --path=storage/app/my_chapters
```

## Key Features

### ✅ **Preserves Existing Logic**
- Uses exact same upload method as controller
- Same R2 storage integration
- Same random filename generation
- Same folder structure: `{comic_id}/chapter-id-{chapter_id}/`

### ✅ **Service Architecture**
- Reusable upload logic
- Easy to test and maintain
- Consistent behavior across interfaces

### ✅ **Error Handling**
- Per-chapter error handling (doesn't stop entire process)
- Progress tracking and statistics
- Validation for comic existence

### ✅ **File Processing**
- Natural file sorting (1, 2, 10, 11)
- Converts local files to UploadedFile instances
- Supports all image formats from controller

## Service Methods

### `upload(int $comicId, string $chapterName, array $images)`
- Creates chapter with images
- Uses DB transactions
- Returns Chapter instance

### `createUploadedFile(string $filePath)`
- Converts local file to UploadedFile
- Simulates real upload behavior

### `chapterExists(int $comicId, int $chapterNumber)`
- Checks for duplicate chapters
- Used for --skip-existing option

### `getNextChapterNumber(int $comicId)`
- Gets next chapter number for a comic

## Controller Changes

### Minimal Impact
- Added service injection
- Modified only the `store()` method
- All other methods unchanged
- Single upload still works exactly the same

### Before (Original Logic)
```php
// Direct image handling in controller
foreach ($files as $file) {
    $path = $this->uploadImage($file, $chapter);
    ChapterImage::create([...]);
}
```

### After (Service Logic)
```php
// Service handles all upload logic
$this->chapterUploadService->upload($comicId, $chapterName, $files);
```

## Command Process

1. **Validation**: Checks comic ID and directory structure
2. **Discovery**: Scans for chapter directories
3. **Conversion**: Creates UploadedFile instances from local files
4. **Upload**: Uses service to maintain exact same logic
5. **Reporting**: Shows detailed progress and results

## Testing

### Directory Created
```
storage/app/bulk_chapters/1/
    Chapter 1/
        README.txt
    Chapter 2/
```

### Command Registration
```bash
php artisan list | findstr chapters
# Should show: chapters:bulk-upload
```

## Production Considerations

### Memory Management
- Processes one chapter at a time
- Uses transactions for data integrity
- Efficient file handling

### Compatibility
- ✅ Does NOT break existing uploads
- ✅ Uses same R2 storage logic
- ✅ Maintains same database schema
- ✅ Preserves all relationships

### Error Recovery
- Individual chapter failures don't stop process
- Detailed error reporting
- Statistics tracking

## Usage Example

```bash
# Upload chapters for comic ID 1
php artisan chapters:bulk-upload --comic_id=1

# Output:
Starting bulk upload for comic: My Manga (ID: 1)
Source directory: /path/to/storage/app/bulk_chapters/1
Skip existing: NO

Found 2 chapter directories
  2/2 [============================] 100%

=== Upload Results ===
Chapters found: 2
Chapters uploaded: 2
Chapters skipped: 0
Images uploaded: 15
Images skipped: 0
✓ Upload completed successfully!
```

## Benefits

1. **Code Reuse**: Single source of truth for upload logic
2. **Maintainability**: Easy to update upload behavior
3. **Testing**: Service can be unit tested independently
4. **Consistency**: Same behavior across all upload interfaces
5. **Future Proof**: Easy to add new upload methods

This implementation follows Laravel best practices and maintains full backward compatibility.
