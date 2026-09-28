-- MySQL Database Schema for iluvgahrie
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `iluvgahrie` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `iluvgahrie`;

-- --------------------------------------------------------
-- Table structure for table `notes`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `timestamp` BIGINT NOT NULL,
    `text` TEXT NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `color` VARCHAR(50) DEFAULT 'pastel-pink',
    `audio` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `media`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
