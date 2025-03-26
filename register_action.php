<?php
include 'includes/functions.php';
include 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $password = cleanInput($_POST['password']);
    $confirm_password = cleanInput($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "Пароли не совпадают!";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $conn = getDbConnection();
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password_hash);

    if ($stmt->execute()) {
        echo "Регистрация прошла успешно!";
    } else {
        echo "Ошибка при регистрации!";
    }
}
?>
