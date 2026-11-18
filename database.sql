-- Badminton GearMate - Week 6 MVP Demo
-- Model specs checked against YONEX source URLs. Demo MYR prices and level tags are illustrative.
CREATE DATABASE IF NOT EXISTS badminton_gearmate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE badminton_gearmate;
CREATE TABLE IF NOT EXISTS rackets (
 id INT AUTO_INCREMENT PRIMARY KEY,
 brand VARCHAR(60) NOT NULL,
 model VARCHAR(150) NOT NULL UNIQUE,
 series VARCHAR(60) NOT NULL,
 balance ENUM('head-heavy','head-light','even') NOT NULL,
 flex ENUM('hi-flex','medium','stiff','extra-stiff') NOT NULL,
 weight VARCHAR(10) NOT NULL,
 level ENUM('beginner','intermediate','advanced') NOT NULL,
 demo_price INT NOT NULL,
 source_url VARCHAR(600) NOT NULL
) ENGINE=InnoDB;
INSERT INTO rackets (id,brand,model,series,balance,flex,weight,level,demo_price,source_url) VALUES
(1,'YONEX','ASTROX 99 PLAY','ASTROX','head-heavy','medium','4U','beginner',260,'https://us.yonex.com/products/astrox-99-play-strung'),
(2,'YONEX','ASTROX 88D GAME','ASTROX','head-heavy','medium','4U','intermediate',460,'https://www.yonex.com/3ax88d-g'),
(3,'YONEX','ASTROX 99 GAME','ASTROX','head-heavy','medium','4U','intermediate',480,'https://us.yonex.com/products/astrox-99-game'),
(4,'YONEX','ASTROX 77 PRO','ASTROX','head-heavy','medium','4U','intermediate',890,'https://us.yonex.com/products/astrox-77-pro'),
(5,'YONEX','ASTROX 88D PRO','ASTROX','head-heavy','stiff','4U','advanced',1080,'https://us.yonex.com/products/astrox-88d-pro'),
(6,'YONEX','ASTROX 88S PRO','ASTROX','head-heavy','stiff','4U','advanced',1020,'https://us.yonex.com/products/astrox-88s-pro'),
(7,'YONEX','ARCSABER 11 PRO','ARCSABER','even','stiff','4U','advanced',1030,'https://us.yonex.com/products/arcsaber-11-pro'),
(8,'YONEX','ARCSABER 7 PRO','ARCSABER','even','medium','4U','intermediate',880,'https://us.yonex.com/products/arcsaber-7-pro'),
(9,'YONEX','NANOFLARE 700 GAME','NANOFLARE','head-light','hi-flex','4U','beginner',380,'https://us.yonex.com/products/nanoflare-700-game'),
(10,'YONEX','NANOFLARE 1000 GAME','NANOFLARE','head-light','medium','4U','intermediate',500,'https://us.yonex.com/products/nanoflare-1000-game'),
(11,'YONEX','NANOFLARE 700 PRO','NANOFLARE','head-light','medium','4U','intermediate',930,'https://us.yonex.com/products/nanoflare-700-pro'),
(12,'YONEX','NANOFLARE 1000 Z','NANOFLARE','head-light','extra-stiff','4U','advanced',1100,'https://us.yonex.com/products/nanoflare-1000-z?_pos=3&_sid=8c0176ac0&_ss=r')
ON DUPLICATE KEY UPDATE brand=VALUES(brand), series=VALUES(series), balance=VALUES(balance), flex=VALUES(flex), weight=VALUES(weight), level=VALUES(level), demo_price=VALUES(demo_price), source_url=VALUES(source_url);


-- Account system migration: safe to run after importing the original Week 6 database.
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 display_name VARCHAR(60) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
