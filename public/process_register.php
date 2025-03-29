<?php
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Проверка совпадения паролей
    if ($password !== $password_confirm) {
        die("Ошибка: Пароли не совпадают!");
    }

    // Хешируем пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Проверяем, существует ли пользователь
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        die("Ошибка: Email уже зарегистрирован!");
    }

    // Добавляем пользователя в БД
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password]);

    echo "Регистрация успешна! <a href='login.php'>Войти</a>";
}
?>
