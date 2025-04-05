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
    $stmt = $conn->prepare("SELECT id, title, status FROM cases WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cases = [];
    while ($row = $result->fetch_assoc()) {
        $cases[] = $row;
    }
    echo json_encode($cases);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $title = $input['title'] ?? '';
    $status = $input['status'] ?? 'open';

    if (empty($title)) {
        die(json_encode(['success' => false, 'message' => 'Название дела обязательно']));
    }

    $stmt = $conn->prepare("INSERT INTO cases (user_id, title, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $title, $status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Дело добавлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления дела']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID дела обязателен']));
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $title = $input['title'] ?? '';
    $status = $input['status'] ?? 'open';

    if (empty($title)) {
        die(json_encode(['success' => false, 'message' => 'Название дела обязательно']));
    }

    $stmt = $conn->prepare("UPDATE cases SET title = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $status, $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Дело обновлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка обновления или дело не найдено']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID дела обязателен']));
    }

    $stmt = $conn->prepare("DELETE FROM cases WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Дело удалено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Дело не найдено']);
    }
    $stmt->close();
}
$conn->close();
?>