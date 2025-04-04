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
    $stmt = $conn->prepare("
    SELECT t.id, t.description, t.status, t.responsible_id, u.username AS responsible 
    FROM tasks t 
    LEFT JOIN users u ON t.responsible_id = u.id 
    WHERE t.user_id = ?
  ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $description = $input['description'] ?? '';
    $responsible_id = $input['responsible_id'] ?? null;

    if (empty($description)) {
        die(json_encode(['success' => false, 'message' => 'Описание задачи обязательно']));
    }

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, description, responsible_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $_SESSION['user_id'], $description, $responsible_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Задача добавлена']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления задачи']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID задачи обязателен']));
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $description = $input['description'] ?? '';
    $status = $input['status'] ?? 'open';
    $responsible_id = $input['responsible_id'] ?? null;

    if (empty($description)) {
        die(json_encode(['success' => false, 'message' => 'Описание задачи обязательно']));
    }

    $stmt = $conn->prepare("UPDATE tasks SET description = ?, status = ?, responsible_id = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssiii", $description, $status, $responsible_id, $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Задача обновлена']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Задача не найдена или не принадлежит вам']);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        die(json_encode(['success' => false, 'message' => 'ID задачи обязателен']));
    }

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Задача удалена']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Задача не найдена или не принадлежит вам']);
    }
    $stmt->close();
}
$conn->close();
?>