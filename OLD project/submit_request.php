<?php
include 'includes/functions.php';
include 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    $category = cleanInput($_POST['category']);
    $user_id = $_SESSION['user_id']; // Идентификатор пользователя из сессии

    $conn = getDbConnection();
    $sql = "INSERT INTO requests (user_id, title, description, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $description, $category);

    if ($stmt->execute()) {
        echo "Заявка успешно подана!";
    } else {
        echo "Ошибка при подаче заявки!";
    }
}
?>
