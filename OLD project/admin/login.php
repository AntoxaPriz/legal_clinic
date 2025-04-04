<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Подключаем базу данных
    include '../includes/database.php';
    $conn = getDbConnection();

    // Получаем данные для входа
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Проверка данных
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit();
    } else {
        $error = "Неверный логин или пароль.";
    }
}

include '../includes/header.php';
?>

<main>
    <h1>Вход в админ-панель</h1>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>

    <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Войти</button>
    </form>
</main>

<?php
include '../includes/footer.php';
?>

