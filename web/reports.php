<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $report = [
        'doc_count' => $conn->query("SELECT COUNT(*) FROM documents WHERE user_id = " . $_SESSION['user_id'])->fetch_row()[0],
        'task_count' => $conn->query("SELECT COUNT(*) FROM tasks WHERE user_id = " . $_SESSION['user_id'])->fetch_row()[0]
    ];
    echo json_encode(['success' => true, 'report' => $report]);
}
$conn->close();
?>