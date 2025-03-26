<?php
include '../includes/database.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id']; // Идентификатор пользователя из сессии
$conn = getDbConnection();
$sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = FALSE ORDER BY created_at DESC"; // Получаем непрочитанные уведомления
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main>
    <h1>Уведомления</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <p><?php echo $row['message']; ?></p>
                <small><?php echo $row['created_at']; ?></small>
                <form action="mark_as_read.php" method="POST">
                    <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Пометить как прочитанное</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
</main>

<?php include '../includes/footer.php'; ?>
