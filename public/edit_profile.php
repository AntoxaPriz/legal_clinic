<?php
session_start();
require '../config/db.php';

// Проверяем авторизацию
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

// Обработка обновления профиля
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_username = trim($_POST["username"]);
    $new_email = trim($_POST["email"]);

    if (!empty($new_username) && !empty($new_email)) {
        $update_stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update_stmt->execute([$new_username, $new_email, $_SESSION["user_id"]]);
        $_SESSION["success"] = "Профиль обновлён!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Все поля обязательны!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать профиль</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="profile-container">
    <h2>Редактирование профиля</h2>
    <?php if (isset($_SESSION["error"])): ?>
        <p class="error"><?= $_SESSION["error"]; unset($_SESSION["error"]); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Имя пользователя:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <button type="submit">Сохранить</button>
    </form>
    <a href="dashboard.php">Назад</a>
</div>
</body>
</html>
