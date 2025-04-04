<?php include 'includes/header.php'; ?>

<main>
    <h1>Чат</h1>
    <div id="chat-container">
        <div id="chat-box">
            <!-- Сообщения будут выводиться сюда -->
        </div>
        <form action="send_message.php" method="POST">
            <textarea name="message" id="message" required></textarea><br>
            <button type="submit">Отправить</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
