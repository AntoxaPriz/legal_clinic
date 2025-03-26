<?php
// Функция для безопасного ввода данных
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Функция для обработки формы входа
function handleLogin($email, $password) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}
?>
