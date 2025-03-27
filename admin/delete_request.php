<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем ID заявки из URL
$request_id = $_GET['id'];

// Удаляем заявку из базы данных
$sql = "DELETE FROM requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $request_id);
$stmt->execute();

// Перенаправляем обратно на страницу списка заявок
header('Location: requests.php');
exit();
?>
