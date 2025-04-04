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
    $stmt = $conn->prepare("SELECT id, title, status FROM cases WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cases = [];
    while ($row = $result->fetch_assoc()) {
        $cases[] = $row;
    }
    echo json_encode($cases);
    $stmt->close();
}
$conn->close();
?>