<?php
include '../includes/header.php';
include '../includes/database.php';

// Функция для добавления уведомлений в базу данных
function addNotification($user_id, $message) {
    $conn = getDbConnection();
    $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);

    return $stmt->execute();
}

$user_id = $_SESSION['user_id']; // Идентификатор пользователя из сессии
$conn = getDbConnection();
$sql = "SELECT * FROM requests WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Пример: Если статус заявки изменился, добавляем уведомление
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Логика изменения статуса заявки
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status']; // Например, "Обработано"

    // Обновление статуса заявки в базе данных
    $sql = "UPDATE requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $request_id);
    $stmt->execute();

    // Добавление уведомления для пользователя
    $message = "Ваш статус заявки был изменён на '{$new_status}'";
    addNotification($user_id, $message); // Добавляем уведомление
}

?>

<main>
    <h1>Мои заявки</h1>
    <table>
        <thead>
        <tr>
            <th>Тема</th>
            <th>Категория</th>
            <th>Статус</th>
            <th>Дата подачи</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php include '../includes/footer.php'; ?>
