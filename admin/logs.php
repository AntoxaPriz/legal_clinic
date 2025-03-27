<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем логи
$sql = "SELECT * FROM admin_activity ORDER BY timestamp DESC";
$result = $conn->query($sql);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Лог действий администраторов</h1>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Администратор</th>
            <th>Действие</th>
            <th>Время</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($log = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $log['id']; ?></td>
                <td><?php echo $log['admin_name']; ?></td>
                <td><?php echo $log['action']; ?></td>
                <td><?php echo $log['timestamp']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</main>

<?php
include '../includes/footer.php';
?>
