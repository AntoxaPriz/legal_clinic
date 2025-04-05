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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $uploadDir = 'uploads/';
    $filePath = $uploadDir . basename($image['name']);

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($image['tmp_name'], $filePath)) {
        $output = shell_exec("python ../scripts/ocr_script.py " . escapeshellarg($filePath));
        echo json_encode(['success' => true, 'text' => trim($output)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки файла']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный запрос']);
}
?>