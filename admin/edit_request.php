<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

// Получаем ID заявки из URL
$request_id = $_GET['id'];

// Получаем информацию о заявке
$sql = "SELECT * FROM requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

// Если заявка не найдена
if (!$request) {
    echo "Заявка не найдена.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы редактирования
    $status = $_POST['status'];

    // Обновляем статус заявки
    $sql = "UPDATE requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $request_id);
    $stmt->execute();

    // Логируем действие
    $admin_name = $_SESSION['admin_name'];  // Получаем имя администратора
    $action = "Изменил статус заявки ID {$request_id} на {$status}";
    $log_sql = "INSERT INTO admin_activity (admin_name, action) VALUES (?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param('ss', $admin_name, $action);
    $log_stmt->execute();

    // Перенаправляем обратно на страницу заявок
    header('Location: requests.php');
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Редактирование заявки #<?php echo $request['id']; ?></h1>
    <form method="POST">
        <label for="status">Статус</label>
        <select name="status" id="status">
            <option value="Ожидает" <?php if ($request['status'] == 'Ожидает') echo 'selected'; ?>>Ожидает</option>
            <option value="В процессе" <?php if ($request['status'] == 'В процессе') echo 'selected'; ?>>В процессе</option>
            <option value="Закрыта" <?php if ($request['status'] == 'Закрыта') echo 'selected'; ?>>Закрыта</option>
        </select><br>

        <button type="submit">Сохранить изменения</button>
    </form>
</main>

<?php
include '../includes/footer.php';
?>
