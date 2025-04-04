<?php
session_start();
include '../includes/header.php';
include '../includes/database.php';
$conn = getDbConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<main>
    <h1>Профиль пользователя</h1>

    <form action="update_profile.php" method="POST">
        <label for="name">ФИО:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

        <label for="email">Электронная почта:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="password">Новый пароль:</label>
        <input type="password" id="password" name="password"><br>

        <label for="confirm_password">Подтверждение пароля:</label>
        <input type="password" id="confirm_password" name="confirm_password"><br>

        <button type="submit">Сохранить изменения</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
