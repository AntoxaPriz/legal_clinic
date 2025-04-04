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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileInput'])) {
    $file = $_FILES['fileInput'];
    $uploadDir = '../uploads/';
    $fileName = time() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Здесь должен быть вызов OCR-сервера (заглушка)
        $text = "Распознанный текст (заглушка)";

        $stmt = $conn->prepare("INSERT INTO documents (user_id, file_path, extracted_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $filePath, $text);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'file' => $fileName, 'text' => $text]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки файла']);
    }
}
$conn->close();
?>