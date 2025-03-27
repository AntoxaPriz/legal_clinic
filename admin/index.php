<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Добро пожаловать в админ-панель</h1>
    <p>Здесь вы можете управлять пользователями, заявками и настройками сайта.</p>

    <div class="statistics">
        <h2>Статистика</h2>
        <p>Количество пользователей: <span>150</span></p>
        <p>Количество заявок: <span>45</span></p>
        <p>Количество активных юристов: <span>10</span></p>
    </div>

    <div class="actions">
        <h3>Действия</h3>
        <ul>
            <li><a href="users.php">Управление пользователями</a></li>
            <li><a href="requests.php">Управление заявками</a></li>
            <li><a href="notifications.php">Уведомления</a></li>
            <li><a href="settings.php">Настройки</a></li>
            <li><a href="reports.php">Отчёты</a></li>
        </ul>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
