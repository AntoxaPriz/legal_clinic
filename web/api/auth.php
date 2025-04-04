<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Выход выполнен']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $token = bin2hex(random_bytes(32));
            echo json_encode(['success' => true, 'token' => $token, 'role' => $user['role']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверный логин или пароль']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверный логин или пароль']);
    }
    $stmt->close();
}
$conn->close();
?>