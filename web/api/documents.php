<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/database.php'; // Вынесите подключение к БД в отдельный файл

header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

// Проверка CSRF-токена
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
}

try {
    $conn = Database::getConnection(); // Используйте класс для управления подключениями

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Получение документов пользователя
        $stmt = $conn->prepare("SELECT id, file_path, extracted_text FROM documents WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Ошибка подготовки запроса');
        }

        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        $documents = [];
        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }

        echo json_encode($documents);
        $stmt->close();

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Удаление документа
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;

        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            die(json_encode(['success' => false, 'message' => 'Неверный ID документа']));
        }

        $stmt = $conn->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            throw new Exception('Ошибка подготовки запроса');
        }

        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Документ не найден']);
        } else {
            echo json_encode(['success' => true]);
        }

        $stmt->close();
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage()); // Логируем ошибку
    echo json_encode(['success' => false, 'message' => 'Внутренняя ошибка сервера']);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>