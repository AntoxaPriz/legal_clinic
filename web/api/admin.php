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

$conn = new mysqli('localhost', 'root', '', 'legal_clinic');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к БД']));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['users'])) {
    if ($_SESSION['role'] !== 'curator') {
        die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
    }

    $stmt = $conn->prepare("SELECT id, username, role FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['role'] !== 'curator') {
        die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $role = $input['role'] ?? 'user';

    if (empty($username) || empty($password)) {
        die(json_encode(['success' => false, 'message' => 'Имя пользователя и пароль обязательны']));
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Пользователь добавлен']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления пользователя']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if ($_SESSION['role'] !== 'curator') {
        die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
    }

    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID пользователя обязателен']));
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? null;
    $role = $input['role'] ?? 'user';

    if (empty($username)) {
        die(json_encode(['success' => false, 'message' => 'Имя пользователя обязательно']));
    }

    $query = "UPDATE users SET username = ?, role = ?";
    $params = [$username, $role];
    $types = "ss";

    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Пользователь обновлён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка обновления или данные не изменились']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if ($_SESSION['role'] !== 'curator') {
        die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
    }

    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID пользователя обязателен']));
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Пользователь удалён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    }
    $stmt->close();
}
$conn->close();
?>