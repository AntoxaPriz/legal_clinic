<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем ID пользователя из URL
$user_id = $_GET['id'];

// Удаляем пользователя из базы данных
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();

// Перенаправляем обратно на страницу списка пользователей
header('Location: users.php');
exit();
?>
