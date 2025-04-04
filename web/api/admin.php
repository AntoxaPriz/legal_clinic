<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещен']));
}

$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
}

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT id, username, role FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}
$conn->close();
?>