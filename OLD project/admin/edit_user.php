<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем ID пользователя из URL
$user_id = $_GET['id'];

// Получаем информацию о пользователе
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Если пользователь не найден
if (!$user) {
    echo "Пользователь не найден.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы редактирования
    $status = $_POST['status'];

    // Обновляем статус пользователя
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $user_id);
    $stmt->execute();

    // Логируем действие
    $admin_name = $_SESSION['admin_name'];  // Получаем имя администратора
    $action = "Изменил статус пользователя ID {$user_id} на {$status}";
    $log_sql = "INSERT INTO admin_activity (admin_name, action) VALUES (?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param('ss', $admin_name, $action);
    $log_stmt->execute();

    // Перенаправляем обратно на список пользователей
    header('Location: users.php');
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Редактирование пользователя #<?php echo $user['id']; ?></h1>
    <form method="POST">
        <label for="status">Статус</label>
        <select name="status" id="status">
            <option value="Активен" <?php if ($user['status'] == 'Активен') echo 'selected'; ?>>Активен</option>
            <option value="Заблокирован" <?php if ($user['status'] == 'Заблокирован') echo 'selected'; ?>>Заблокирован</option>
        </select><br>

        <button type="submit">Сохранить изменения</button>
    </form>
</main>

<?php
include '../includes/footer.php';
?>
