-- =============================================================================
-- MIGRATION: light/dark theme preference
--
-- Run this once on an existing installation to add the theme column to the
-- per-user settings table. New installations already have it, because
-- database/du_lieu.sql ships with the column included.
--
--   mysql -u root -p game_distribution_platform_db < database/migration_giao_dien.sql
-- =============================================================================

ALTER TABLE `cai_dat_nguoi_dung`
  ADD COLUMN `giao_dien` enum('dark','light') NOT NULL DEFAULT 'light' AFTER `tien_te_uu_tien`;
