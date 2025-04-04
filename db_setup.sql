CREATE DATABASE IF NOT EXISTS legal_clinic;
USE legal_clinic;

CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       role ENUM('client', 'student', 'curator') DEFAULT 'client'
);

CREATE TABLE documents (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT,
                           file_path VARCHAR(255) NOT NULL,
                           extracted_text TEXT,
                           upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE tasks (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       user_id INT,
                       description TEXT NOT NULL,
                       status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
                       responsible VARCHAR(50),
                       FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (username, password, role) VALUES
                                                 ('client', '789', 'client'),
                                                 ('student', '456', 'student'),
                                                 ('curator', '123', 'curator');