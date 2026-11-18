-- GearMate v0.4 new features, once only, after database.sql, users_migration.sql,
-- v03_migration.sql and v031_catalog_migration.sql. Does not delete old data.
USE badminton_gearmate;
CREATE TABLE IF NOT EXISTS gear_setup_cards (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  racket_id INT NOT NULL,
  setup_json LONGTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_gear_setup_user (user_id,id),
  CONSTRAINT fk_gear_setup_owner FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS saved_rackets (
  user_id INT NOT NULL,
  racket_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id,racket_id),
  CONSTRAINT fk_saved_racket_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS racket_spec_limits (
  racket_id INT PRIMARY KEY,
  min_lbs SMALLINT UNSIGNED NOT NULL,
  max_lbs SMALLINT UNSIGNED NOT NULL,
  official_url VARCHAR(600) NOT NULL,
  verified_date DATE NOT NULL,
  status ENUM('official_verified','needs_review') DEFAULT 'needs_review',
  CONSTRAINT fk_spec_racket FOREIGN KEY (racket_id) REFERENCES rackets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- No official tension data are prefilled. Each exact variant must be verified before adding.
CREATE TABLE IF NOT EXISTS gear_admin_users (
  user_id INT NOT NULL PRIMARY KEY,
  CONSTRAINT fk_gear_admin FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Optionally grant admin after registering your own account, from phpMyAdmin:
-- INSERT IGNORE INTO gear_admin_users(user_id) SELECT id FROM users WHERE email='your-email@example.com';
-- Only grant to a trusted owner account; never expose this operation to visitors.
-- Add language preference only if it is absent, portable across MySQL versions:
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='preferred_language');
SET @alter_cmd := IF(@col_exists=0,
  'ALTER TABLE users ADD COLUMN preferred_language VARCHAR(5) NOT NULL DEFAULT ''en''',
  'SELECT 1');
PREPARE stmt FROM @alter_cmd;EXECUTE stmt;DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS racket_cn_names (
  model VARCHAR(150) PRIMARY KEY,
  verified_zh_name VARCHAR(180) NOT NULL,
  name_source_url VARCHAR(600) NOT NULL,
  checked_date DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- No unverified Chinese translations are inserted. Editors can add source-verified names.

CREATE TABLE IF NOT EXISTS racket_photos (
  model VARCHAR(150) PRIMARY KEY,
  photo_url VARCHAR(1000) NOT NULL,
  photo_source_url VARCHAR(600) NOT NULL,
  checked_date DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Populate only with model-matched photographs after checking attribution and usage rights.

-- Source-checked reference prices for additional DIY accessories (2026-11-04).
-- Listed prices are NOT live quotes and do NOT include stringing labour.
INSERT INTO accessory_market_prices(item_code,item_type,display_name,price_rm,price_source,price_url,price_checked,note_zh,note_en) VALUES
('VBS63','string','VICTOR VBS-63',45.00,'Victor Sport Malaysia','https://www.victorsports.my/product/victor-badminton-strings-vbs-63-string','2026-11-04','0.63mm、10m；高反弹取向、手感适中，细线应留意磨损。','0.63mm x 10m; high-resilience, medium feel; monitor wear on thin gauges.'),
('VBS66N','string','VICTOR VBS-66 NANO',45.00,'Victor Sport Malaysia','https://www.victorsports.my/collection/vbs-series-strings','2026-11-04','0.66mm；偏综合使用，具体手感依使用者和磅数而异。','0.66mm; general-purpose option; feel varies with player and tension.'),
('AC402EX','grip','YONEX AC402EX TOWEL GRIP',12.00,'Vsmash Sports','https://vsmash.com/product/yonex-grips-ac402ex-light-blue','2026-11-04','单条棉质毛巾胶；强调吸汗，需注意清洁和更换。','Single cotton towel grip; sweat-absorbent, requires cleaning and replacement.'),
('AC402DX','grip','YONEX AC402DX TOWEL GRIP DX',21.00,'Vsmash Sports','https://vsmash.com/product/yonex-japan-ac402dx-towel-grip-white','2026-11-04','日本款棉质毛巾胶，30mm宽、1.35mm厚；重视吸汗手感。','Japanese cotton towel grip, 30mm wide and 1.35mm thick; absorbent feel.')
ON DUPLICATE KEY UPDATE price_rm=VALUES(price_rm),price_source=VALUES(price_source),price_url=VALUES(price_url),price_checked=VALUES(price_checked),note_zh=VALUES(note_zh),note_en=VALUES(note_en);
