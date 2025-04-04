<?php
include '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notification_id = $_POST['notification_id']; // ID уведомления

    $conn = getDbConnection();
    $sql = "UPDATE notifications SET is_read = TRUE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);

    if ($stmt->execute()) {
        header("Location: index.php"); // Перенаправление обратно на страницу уведомлений
    } else {
        echo "Ошибка при помечении уведомления!";
    }
}
?>
