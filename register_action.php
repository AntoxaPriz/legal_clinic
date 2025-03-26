<?php
// Проверка CSRF токена
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ошибка CSRF');
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка совпадения паролей
    if ($password !== $confirm_password) {
        die("Пароли не совпадают!");
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Подключение к базе данных
    include 'includes/database.php';
    $conn = getDbConnection();

    // Подготовленный запрос для вставки данных
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка регистрации!";
    }
}
?>
