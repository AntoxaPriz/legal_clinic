<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'client')");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
}
$conn->close();
?>