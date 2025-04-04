<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['email' => 'info@legalclinic.com', 'phone' => '+7 (123) 456-78-90']);
}
?>