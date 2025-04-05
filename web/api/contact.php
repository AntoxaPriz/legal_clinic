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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $contacts = [
        'email' => 'info@legalclinic.com',
        'phone' => '+7 (123) 456-78-90',
        'address' => 'ул. Примерная, д. 1, Москва'
    ];
    echo json_encode($contacts);
}
?>