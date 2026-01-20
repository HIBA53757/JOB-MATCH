CREATE DATABASE  IF NOT EXISTS router;
USE router;
--@block

CREATE TABLE users (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(255)  NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `role` ENUM('ADMIN','APPRENANT') NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--@block
CREATE TABLE `entreprise` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `secteur` VARCHAR(255),
    `localisation` VARCHAR(255),
    `email` VARCHAR(255),
    `telephone` VARCHAR(50)
);

--@block
CREATE TABLE `annonce` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `typeContrat` VARCHAR(100),
    `localisation` VARCHAR(255),
    `image` VARCHAR(255),
    `competences` TEXT,
    `deleted` BOOLEAN DEFAULT FALSE,
    `dateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `dateUpdate` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `entreprise_id` INT,
    FOREIGN KEY (`entreprise_id`) REFERENCES `entreprise`(`id`) ON DELETE CASCADE
);