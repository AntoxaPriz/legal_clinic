<?php
include '../includes/header.php';
include '../includes/database.php';

$chat_id = $_GET['chat_id']; // Идентификатор чата, передается в URL
$conn = getDbConnection();
$sql = "SELECT * FROM chat_messages WHERE chat_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main>
    <h1>Чат</h1>
    <div id="chat-box">
        <?php while ($row = $result->fetch_assoc()): ?>
            <p><strong><?php echo $row['user_name']; ?>:</strong> <?php echo $row['message']; ?></p>
        <?php endwhile; ?>
    </div>
    <form action="send_message.php?chat_id=<?php echo $chat_id; ?>" method="POST">
        <textarea name="message" id="message" required></textarea><br>
        <button type="submit">Отправить</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
