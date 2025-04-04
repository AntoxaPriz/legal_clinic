<?php
// login.php
require_once 'config.php';

// Проверяем настройки
$sql = "SELECT * FROM settings WHERE id = 1";
$stmt = $pdo->query($sql);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login']; // Логин, который может быть email, телефон или обычный логин
    $password = $_POST['password'];

    // Проверяем, если включена аутентификация через email или телефон
    if (filter_var($login, FILTER_VALIDATE_EMAIL) && $settings['enable_email_auth']) {
        // Аутентификация через email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $login);
    } elseif (preg_match('/^[0-9]+$/', $login) && $settings['enable_phone_auth']) {
        // Аутентификация через телефон
        $sql = "SELECT * FROM users WHERE phone = :phone";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':phone', $login);
    } else {
        // Аутентификация через логин
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $login);
    }

    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo "Добро пожаловать, " . $user['username'];
    } else {
        echo "Неверный логин или пароль.";
    }
}
?>

<!-- Форма входа -->
<form action="login.php" method="POST">
    <label for="login">Логин, email или телефон:</label>
    <input type="text" id="login" name="login" required>
    <br>
    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Войти</button>
</form>
