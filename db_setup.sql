-- Создание базы данных, если ещё не существует
CREATE DATABASE IF NOT EXISTS legal_clinic;
USE legal_clinic;

-- Таблица пользователей (users)
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL, -- Хранит хешированные пароли
                       role ENUM('admin', 'director', 'curator', 'student') DEFAULT 'student',
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица клиентов (clients) с полями для аутентификации
CREATE TABLE clients (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         username VARCHAR(50) NOT NULL UNIQUE, -- Для входа клиента
                         password VARCHAR(255) NOT NULL,       -- Хешированный пароль
                         name VARCHAR(255) NOT NULL,
                         email VARCHAR(255),
                         phone VARCHAR(50),
                         user_id INT NOT NULL, -- Ссылка на пользователя, добавившего клиента
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблица обращений (requests)
CREATE TABLE requests (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          client_id INT NOT NULL,          -- Ссылка на клиента, создавшего обращение
                          description TEXT,                -- Описание обращения
                          status ENUM('new', 'in_progress', 'completed') DEFAULT 'new',
                          solution_file VARCHAR(255),      -- Путь к файлу готового решения
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Таблица документов (documents) с поддержкой загрузки клиентами
CREATE TABLE documents (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT,                    -- Ссылка на пользователя, загрузившего документ (может быть NULL)
                           client_id INT,                  -- Ссылка на клиента, загрузившего документ (может быть NULL)
                           request_id INT,                 -- Ссылка на обращение (может быть NULL)
                           file_path VARCHAR(255) NOT NULL,
                           extracted_text TEXT,
                           upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                           FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
                           FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
);

-- Таблица задач (tasks)
CREATE TABLE tasks (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       user_id INT NOT NULL, -- Ссылка на пользователя, создавшего задачу
                       description TEXT NOT NULL,
                       status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
                       responsible_id INT, -- Ссылка на ответственного пользователя
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                       FOREIGN KEY (responsible_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Начальные данные для users (с хешированными паролями)
INSERT INTO users (username, password, role) VALUES
                                                 ('admin', '$2y$10$X8gX8gX8gX8gX8gX8gX8gXuX8gX8gX8gX8gX8gX8gX', 'admin'),     -- Пример хеша для 'admin123'
                                                 ('director', '$2y$10$Y9hY9hY9hY9hY9hY9hY9hYvY9hY9hY9hY9hY9hY9hY', 'director'), -- Пример хеша для 'dir123'
                                                 ('curator', '$2y$10$Z7iZ7iZ7iZ7iZ7iZ7iZ7iZwZ7iZ7iZ7iZ7iZ7iZ7iZ', 'curator'),  -- Пример хеша для 'cur123'
                                                 ('student', '$2y$10$A6jA6jA6jA6jA6jA6jA6jAxA6jA6jA6jA6jA6jA6jA', 'student');  -- Пример хеша для 'stu123'

-- Пример данных для clients (с добавленными username и password)
INSERT INTO clients (username, password, name, email, phone, user_id) VALUES
                                                                          ('ivanov', '$2y$10$B5kB5kB5kB5kB5kB5kB5kByB5kB5kB5kB5kB5kB5kB', 'Иванов Иван', 'ivanov@example.com', '+7 (900) 123-45-67', 3), -- Добавил куратор
                                                                          ('petrov', '$2y$10$C4lC4lC4lC4lC4lC4lC4lCzC4lC4lC4lC4lC4lC4lC', 'Петров Пётр', 'petrov@example.com', '+7 (900) 987-65-43', 4); -- Добавил студент

-- Пример данных для requests
INSERT INTO requests (client_id, description, status, solution_file) VALUES
                                                                         (1, 'Подготовить договор аренды', 'new', NULL),               -- Обращение Иванова
                                                                         (2, 'Проверить трудовой договор', 'in_progress', NULL);       -- Обращение Петрова

-- Пример данных для documents
INSERT INTO documents (user_id, client_id, request_id, file_path, extracted_text) VALUES
                                                                                      (4, NULL, NULL, '/uploads/doc1.pdf', 'Текст документа 1'),    -- Загрузил студент
                                                                                      (3, NULL, NULL, '/uploads/doc2.pdf', 'Текст документа 2'),    -- Загрузил куратор
                                                                                      (NULL, 1, 1, '/uploads/client_doc1.pdf', NULL),              -- Загрузил клиент Иванов
                                                                                      (NULL, 2, 2, '/uploads/client_doc2.pdf', NULL);              -- Загрузил клиент Петров

-- Пример данных для tasks
INSERT INTO tasks (user_id, description, status, responsible_id) VALUES
                                                                     (3, 'Подготовить договор', 'open', 4),         -- Куратор создал, студент отвечает
                                                                     (4, 'Проверить документы', 'in_progress', 3);  -- Студент создал, куратор отвечает