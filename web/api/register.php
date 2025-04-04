<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
}

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Добавлено

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
    $stmt->bind_param("ss", $username, $hashed_password); // Используем хеш
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Регистрация успешна']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка регистрации']);
    }
    $stmt->close();
}
$conn->close();
?>