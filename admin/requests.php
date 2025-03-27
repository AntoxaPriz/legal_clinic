<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

$status_filter = '';
$search_query = '';

// Проверка наличия фильтров
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Формируем SQL запрос с фильтром и поиском
$sql = "SELECT * FROM requests WHERE subject LIKE ? ";
if ($status_filter) {
    $sql .= "AND status = ? ";
}
$sql .= "ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
if ($status_filter) {
    $stmt->bind_param('ss', $search_param, $status_filter);
} else {
    $stmt->bind_param('s', $search_param);
}
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Управление заявками</h1>

    <form method="GET">
        <input type="text" name="search" placeholder="Поиск по теме" value="<?php echo $search_query; ?>">
        <select name="status">
            <option value="">Все статусы</option>
            <option value="Ожидает" <?php if ($status_filter == 'Ожидает') echo 'selected'; ?>>Ожидает</option>
            <option value="В процессе" <?php if ($status_filter == 'В процессе') echo 'selected'; ?>>В процессе</option>
            <option value="Закрыта" <?php if ($status_filter == 'Закрыта') echo 'selected'; ?>>Закрыта</option>
        </select>
        <button type="submit">Фильтровать</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя клиента</th>
            <th>Тема</th>
            <th>Статус</th>
            <th>Дата подачи</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($request = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $request['id']; ?></td>
                <td><?php echo $request['client_name']; ?></td>
                <td><?php echo $request['subject']; ?></td>
                <td><?php echo $request['status']; ?></td>
                <td><?php echo $request['created_at']; ?></td>
                <td>
                    <a href="view_request.php?id=<?php echo $request['id']; ?>">Просмотр</a> |
                    <a href="edit_request.php?id=<?php echo $request['id']; ?>">Редактировать</a> |
                    <a href="delete_request.php?id=<?php echo $request['id']; ?>">Удалить</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</main>

<?php
include '../includes/footer.php';
?>
