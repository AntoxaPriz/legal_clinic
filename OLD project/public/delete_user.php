<?php
session_start();
require '../config/db.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Удаление пользователя по ID
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Удаляем пользователя
    $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->execute([$user_id]);

    $_SESSION["success"] = "Пользователь удалён!";
    header("Location: admin.php");
    exit();
} else {
    echo "Неверный запрос.";
    exit();
}
?>
