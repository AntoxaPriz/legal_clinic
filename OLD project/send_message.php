<?php
include 'includes/functions.php';
include 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = cleanInput($_POST['message']);
    $user_id = $_SESSION['user_id']; // Идентификатор пользователя из сессии
    $chat_id = $_GET['chat_id']; // Идентификатор чата, передается в URL

    $conn = getDbConnection();
    $sql = "INSERT INTO chat_messages (chat_id, user_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $chat_id, $user_id, $message);

    if ($stmt->execute()) {
        echo "Сообщение отправлено!";
    } else {
        echo "Ошибка при отправке сообщения!";
    }
}
?>
