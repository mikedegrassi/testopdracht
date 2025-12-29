-- Maak database als deze niet bestaat
CREATE DATABASE IF NOT EXISTS testopdracht
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Gebruik de database
USE testopdracht;

-- Verwijder tabel indien deze al bestaat (handig voor lokaal opnieuw importeren)
DROP TABLE IF EXISTS submissions;

-- Maak tabel voor form submissions (aanhef, voornaam, tussenvoegsel, achternaam, email, land + timestamp)
CREATE TABLE submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    salutation VARCHAR(10) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(50) NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);