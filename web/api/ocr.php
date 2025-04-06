<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    die(json_encode(['success' => false, 'message' => 'Недействительный CSRF-токен']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $user_id = $_SESSION['user_id'];
    $image = $_FILES['image'];
    $uploadDir = 'uploads/';
    $filePath = $uploadDir . basename($image['name']);
    $psm = isset($_POST['psm']) ? (int)$_POST['psm'] : 6;

    // Создание директории, если не существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Перемещение файла
    if (move_uploaded_file($image['tmp_name'], $filePath)) {
        // Вызов Python-скрипта для OCR
        $output = shell_exec("python ../scripts/ocr_script.py " . escapeshellarg($filePath) . " " . $psm);
        $text = trim($output);

        // Подключение к базе данных
        $db = get_db_connection();
        if (!$db) {
            echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе']);
            exit;
        }

        // Сохранение файла и текста в таблицу documents
        $stmt = $db->prepare("INSERT INTO documents (user_id, file_path, extracted_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $filePath, $text);

        if ($stmt->execute()) {
            $document_id = $db->insert_id;
            echo json_encode([
                'success' => true,
                'text' => $text,
                'filePath' => $filePath,
                'document_id' => $document_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка сохранения в базу: ' . $db->error]);
        }

        $stmt->close();
        $db->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки файла']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный запрос']);
}
?>