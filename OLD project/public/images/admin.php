<?php
session_start();
require '../config/db.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['success'])): ?>
    <p class="success"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <p class="error"><?= $_SESSION['error']; ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

// Получаем список пользователей
$users = $pdo->query("SELECT id, username, email, role FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="admin-container">
    <h2>Админ-панель</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>">Редактировать</a> |
                    <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">Удалить</a>
                </td>
            </tr>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['blocked'] ? 'Заблокирован' : 'Активен' ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>">Редактировать</a> |
                    <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="dashboard.php">Назад</a>
</div>
</body>
</html>
