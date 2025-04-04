<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'curator') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещен']));
}

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'client';

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
} else {
    $result = $conn->query("SELECT id, username, role FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}
$conn->close();
?>