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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT id, name, email, phone FROM clients WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    echo json_encode($clients);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? null;
    $phone = $input['phone'] ?? null;

    if (empty($name)) {
        die(json_encode(['success' => false, 'message' => 'Имя клиента обязательно']));
    }

    $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $phone, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Клиент добавлен']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления клиента']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID клиента обязателен']));
    }

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Клиент удалён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Клиент не найден или не принадлежит вам']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID клиента обязателен']));
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? null;
    $phone = $input['phone'] ?? null;

    if (empty($name)) {
        die(json_encode(['success' => false, 'message' => 'Имя клиента обязательно']));
    }

    $stmt = $conn->prepare("UPDATE clients SET name = ?, email = ?, phone = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $name, $email, $phone, $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Клиент обновлён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Клиент не найден или не принадлежит вам']);
    }
    $stmt->close();
}
$conn->close();
?>