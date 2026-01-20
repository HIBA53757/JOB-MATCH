CREATE DATABASE IF NOT EXISTS YouCodeJobDating;
USE YouCodeJobDating;


CREATE TABLE users (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('ADMIN', 'APPRENANT') NOT NULL DEFAULT 'APPRENANT',
    `specialization` VARCHAR(100) NULL,
    `promotion` VARCHAR(50) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `company` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `secteur` VARCHAR(255) NOT NULL,
    `logo` VARCHAR(255) DEFAULT 'default_logo.png',
    `location` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `telephone` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `annonce` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `contract_type` ENUM('CDI', 'CDD', 'Stage', 'Anapec', 'Freelance') NOT NULL, -- استخدام ENUM أفضل للفلترة
    `location` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255) NULL,
    `skills` TEXT NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `company_id` INT NOT NULL,
    
    
    FOREIGN KEY (`company_id`) REFERENCES `company`(`id`) ON DELETE CASCADE
);