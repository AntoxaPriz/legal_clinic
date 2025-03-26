CREATE DATABASE legal_clinic;
USE legal_clinic;

-- Таблица пользователей
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       role ENUM('client', 'lawyer', 'admin') NOT NULL,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(255) UNIQUE NOT NULL,
                       password_hash VARCHAR(255) NOT NULL,
                       phone VARCHAR(20),
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица обращений
CREATE TABLE requests (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          client_id INT NOT NULL,
                          lawyer_id INT DEFAULT NULL,
                          status ENUM('new', 'in_progress', 'closed') DEFAULT 'new',
                          description TEXT NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
                          FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Таблица документов
CREATE TABLE documents (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           request_id INT NOT NULL,
                           file_name VARCHAR(255) NOT NULL,
                           file_path VARCHAR(255) NOT NULL,
                           uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
);

-- Таблица отчетов
CREATE TABLE reports (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         lawyer_id INT NOT NULL,
                         request_id INT NOT NULL,
                         report_text TEXT NOT NULL,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE CASCADE,
                         FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
);

-- Таблица уведомлений
CREATE TABLE notifications (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               user_id INT NOT NULL,
                               message TEXT NOT NULL,
                               status ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

