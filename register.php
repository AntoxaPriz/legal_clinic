<?php include 'includes/header.php'; ?>

<main>
    <h1>Регистрация</h1>

    <?php
    // Генерация CSRF токена, если его еще нет
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>

    <form action="register_action.php" method="POST">
        <!-- CSRF токен -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label for="name">ФИО:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="email">Электронная почта:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">Подтверждение пароля:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>

        <button type="submit">Зарегистрироваться</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>

