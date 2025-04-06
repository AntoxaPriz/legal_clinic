<?php
session_start();

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_db_connection() {
    $host = 'localhost';        // Хост базы данных
    $username = 'root';         // Имя пользователя MySQL (замени на своё)
    $password = '';             // Пароль MySQL (замени на свой)
    $database = 'legal_clinic'; // Имя базы данных

    $db = new mysqli($host, $username, $password, $database);
    if ($db->connect_error) {
        return false; // Ошибка подключения
    }
    $db->set_charset('utf8'); // Устанавливаем кодировку UTF-8
    return $db;
}
?>