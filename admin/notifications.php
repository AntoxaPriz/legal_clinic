<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Подключаем базу данных
    include '../includes/database.php';
    $conn = getDbConnection();

    // Получаем данные для уведомления
    $title = $_POST['title'];
    $message = $_POST['message'];
    $user_id = $_POST['user_id'];

    // Добавляем уведомление в базу данных
    $sql = "INSERT INTO notifications (user_id, title, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $user_id, $title, $message);
    $stmt->execute();

    // Перенаправляем обратно на страницу уведомлений
    header('Location: notifications.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем всех пользователей для выбора получателя уведомления
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<main>
    <h1>Создание уведомления</h1>
    <form method="POST">
        <label for="user_id">Пользователь</label>
        <select name="user_id" id="user_id">
            <?php while ($user = $result->fetch_assoc()) { ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
            <?php } ?>
        </select><br>

        <label for="title">Заголовок</label>
        <input type="text" name="title" id="title" required><br>

        <label for="message">Сообщение</label>
        <textarea name="message" id="message" required></textarea><br>

        <button type="submit">Отправить уведомление</button>
    </form>
</main>

<?php
include '../includes/footer.php';
?>
