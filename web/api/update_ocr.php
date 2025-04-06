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

$input = json_decode(file_get_contents('php://input'), true);
$document_id = $input['document_id'] ?? 0;
$extracted_text = $input['extracted_text'] ?? '';

if ($document_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID документа']);
    exit;
}

$db = get_db_connection();
if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе']);
    exit;
}

$stmt = $db->prepare("UPDATE documents SET extracted_text = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $extracted_text, $document_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления: ' . $db->error]);
}

$stmt->close();
$db->close();
?>