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

// Пример отчета по заявкам
$sql = "SELECT status, COUNT(*) as count FROM requests GROUP BY status";
$result = $conn->query($sql);
?>

<main>
    <h1>Отчёты</h1>

    <h2>Отчёт по заявкам</h2>
    <table>
        <thead>
        <tr>
            <th>Статус</th>
            <th>Количество</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($report = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $report['status']; ?></td>
                <td><?php echo $report['count']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</main>

<?php
include '../includes/footer.php';
?>
