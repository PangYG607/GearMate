-- Run this ONCE after the original database.sql / v0.2 users_migration.sql.
-- Creates new user-linked assessment history and saved DIY configurations.
USE badminton_gearmate;
CREATE TABLE IF NOT EXISTS player_assessments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  answers_json LONGTEXT NOT NULL,
  profile_json LONGTEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_player_assessments_user (user_id,id),
  CONSTRAINT fk_assessment_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS saved_setups (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  racket_id INT NOT NULL,
  racket_model VARCHAR(150) NOT NULL,
  string_name VARCHAR(100) NOT NULL,
  string_tension INT NOT NULL,
  grip_name VARCHAR(100) NOT NULL,
  grip_color VARCHAR(40) NOT NULL,
  demo_total INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_saved_setups_user (user_id,id),
  CONSTRAINT fk_setup_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
