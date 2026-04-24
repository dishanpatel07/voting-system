CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    has_voted BOOLEAN DEFAULT 0
);

CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    party VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    votes INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Default admin: username: admin, password: admin123
INSERT IGNORE INTO admin (username, password) VALUES ('admin', '$2y$10$D2B9M9x.W4xG.5d4eT6I/uO.4O3vT.5Hj4T3V/rJ1V.r9Y4g2aW5O');