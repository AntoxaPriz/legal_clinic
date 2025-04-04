<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] ?? 'ru';
    // Здесь можно сохранить настройки в БД (заглушка)
    echo json_encode(['success' => true, 'language' => $language]);
}
?>