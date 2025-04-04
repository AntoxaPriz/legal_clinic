<?php
// register.php
require_once 'config.php';

// Проверяем настройки
$sql = "SELECT * FROM settings WHERE id = 1";
$stmt = $pdo->query($sql);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;

    // Хэшируем пароль перед сохранением
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, phone, password) VALUES (:username, :email, :phone, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка регистрации.";
    }
}
?>

<!-- Форма регистрации -->
<form action="register.php" method="POST">
    <label for="username">Имя пользователя:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="email">Электронная почта:</label>
    <input type="email" id="email" name="email">
    <br>
    <label for="phone">Номер телефона:</label>
    <input type="text" id="phone" name="phone">
    <br>
    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Зарегистрироваться</button>
</form>
