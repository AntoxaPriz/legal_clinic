<?php
session_start();

// Проверка CSRF токена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ошибка CSRF');
    }

    // Получение данных из формы
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

    // Проверка на уникальность email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Этот email уже зарегистрирован!");
    }

    // Подготовленный запрос для вставки данных
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Создание сессии
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name'] = $name;
        echo "Регистрация успешна! Перенаправление...";
        header("Location: dashboard/index.php");
        exit;
    } else {
        echo "Ошибка регистрации!";
    }
}
?>
