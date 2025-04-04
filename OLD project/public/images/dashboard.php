<?php
session_start();
require '../config/db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="dashboard-container">
    <h2>Добро пожаловать, <?= htmlspecialchars($user['username']) ?>!</h2>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <a href="edit_profile.php">Редактировать профиль</a> |
    <a href="logout.php">Выйти</a>
</div>
</body>
</html>

