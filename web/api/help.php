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
    $help = [
        'faq' => [
            ['question' => 'Как добавить задачу?', 'answer' => 'Перейдите в раздел "Задачи" и заполните форму.'],
            ['question' => 'Как выйти из системы?', 'answer' => 'Нажмите кнопку "Выйти" в меню.']
        ]
    ];
    echo json_encode($help);
}
?>