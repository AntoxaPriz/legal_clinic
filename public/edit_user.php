<?php
session_start();
require '../config/db.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Получаем данные пользователя по ID
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT id, username, email, role, blocked FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Пользователь не найден!";
        exit();
    }
}

// Обработка обновления данных пользователя
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_role = $_POST["role"];
    $blocked = isset($_POST["blocked"]) ? 1 : 0;

    // Обновляем роль и статус блокировки
    $update_stmt = $pdo->prepare("UPDATE users SET role = ?, blocked = ? WHERE id = ?");
    if ($update_stmt->execute([$new_role, $blocked, $user_id])) {
        $_SESSION["success"] = "Данные пользователя обновлены!";
    } else {
        $_SESSION["error"] = "Произошла ошибка при обновлении данных.";
    }

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="edit-user-container">
    <h2>Редактирование пользователя</h2>

    <!-- Выводим сообщения об успехе или ошибке -->
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <p class="error"><?= $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post">
        <label>Имя пользователя:</label>
        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>

        <label>Email:</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>

        <label>Роль:</label>
        <select name="role">
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
            <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Преподаватель</option>
            <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Студент</option>
            <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Клиент</option>
        </select>

        <label>
            <input type="checkbox" name="blocked" <?= $user['blocked'] ? 'checked' : '' ?>>
            Заблокировать пользователя
        </label>

        <button type="submit">Сохранить изменения</button>
    </form>
    <a href="admin.php">Назад</a>
</div>
</body>
</html>
