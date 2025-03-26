<?php
// Проверка CSRF токена
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ошибка CSRF');
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Подключение к базе данных
    include 'includes/database.php';
    $conn = getDbConnection();

    // Подготовленный запрос для получения данных пользователя
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Проверка пароля
    if ($user && password_verify($password, $user['password'])) {
        // Успешный вход
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard/index.php"); // Перенаправление в личный кабинет
    } else {
        echo "Неверный логин или пароль!";
    }
}
?>
