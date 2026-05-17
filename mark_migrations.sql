-- SQL script to mark existing migrations as complete
-- Excludes the 3 new view tracking migrations created on 2026_05_09

-- Insert existing migrations as batch 1
INSERT INTO migrations (migration, batch) VALUES 
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_01_17_171519_create_comics_table', 1),
('2026_01_17_171525_create_chapters_table', 1),
('2026_01_17_171532_create_pages_table', 1),
('2026_01_17_171627_add_is_admin_to_users_table', 1),
('2026_01_31_000000_create_comic_bookmarks_table', 1),
('2026_01_31_020000_add_role_to_users', 1),
('2026_02_12_200515_add_slug_to_comics_table', 1),
('2026_04_01_210000_add_user_id_to_comics_table', 1),
('2026_04_01_210001_add_user_id_to_chapters_table', 1)
ON DUPLICATE KEY UPDATE batch = VALUES(batch);

-- Note: The following 3 migrations are NOT included as they should be run fresh:
-- 2026_05_09_210000_add_view_counts_to_comics_and_chapters_table
-- 2026_05_09_210100_create_view_tracking_table  
-- 2026_05_09_210200_add_default_viewer_role_to_users
