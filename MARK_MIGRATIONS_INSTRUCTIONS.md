# Mark Existing Migrations as Complete

## Instructions

### Option 1: Using Laravel Artisan Command (Recommended)

Run this command to mark all existing migrations as complete:

```bash
php artisan migrations:mark-existing
```

This will:
- Mark all existing migrations (except the 3 new view tracking ones) as complete
- Use batch number 2 (assuming batch 1 already exists)
- Leave the 3 new migrations unmarked so they can be run fresh

### Option 2: Using SQL File

If you prefer to use SQL directly, run the contents of `mark_migrations.sql` in your database:

```sql
-- Copy and paste the contents of mark_migrations.sql into your MySQL client
```

### Option 3: Manual PHP Script

Run the PHP script directly:

```bash
php mark_existing_migrations_complete.php
```

## After Marking Migrations

Once the existing migrations are marked as complete, run the new view tracking migrations:

```bash
php artisan migrate
```

This will run only the 3 new migrations:
1. `2026_05_09_210000_add_view_counts_to_comics_and_chapters_table`
2. `2026_05_09_210100_create_view_tracking_table`
3. `2026_05_09_210200_add_default_viewer_role_to_users`

## Verification

After running, you can verify:

```bash
php artisan migrate:status
```

You should see:
- All existing migrations marked as "Ran"
- The 3 new migrations also marked as "Ran"

## Clean Up

After successful completion, you can delete these temporary files:
- `mark_existing_migrations_complete.php`
- `mark_migrations.sql`
- `MARK_MIGRATIONS_INSTRUCTIONS.md`
