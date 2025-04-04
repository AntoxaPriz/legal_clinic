<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
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
    // Пример отчета: количество задач и документов
    $stmt = $conn->prepare("SELECT (SELECT COUNT(*) FROM tasks WHERE user_id = ?) as tasks_count, (SELECT COUNT(*) FROM documents WHERE user_id = ?) as docs_count");
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
    echo json_encode($report ?: []);
    $stmt->close();
}
$conn->close();
?>