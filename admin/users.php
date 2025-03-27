<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';

// Подключаем базу данных
include '../includes/database.php';
$conn = getDbConnection();

// Получаем список пользователей
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<main>
    <h1>Управление пользователями</h1>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Дата регистрации</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td><?php echo $user['status']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Редактировать</a> |
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>">Удалить</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</main>

<?php
include '../includes/footer.php';
?>
