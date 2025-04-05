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
    $stmt = $conn->prepare("SELECT theme FROM user_settings WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc() ?? ['theme' => 'light'];
    echo json_encode($settings);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $theme = $input['theme'] ?? 'light';

    $stmt = $conn->prepare("INSERT INTO user_settings (user_id, theme) VALUES (?, ?) ON DUPLICATE KEY UPDATE theme = ?");
    $stmt->bind_param("iss", $_SESSION['user_id'], $theme, $theme);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode(['success' => true, 'message' => 'Настройки сохранены']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка сохранения настроек']);
    }
    $stmt->close();
}
$conn->close();
?>