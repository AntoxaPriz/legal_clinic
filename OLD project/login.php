<?php include 'includes/header.php'; ?>

<main>
    <h1>Вход в личный кабинет</h1>

    <?php
    // Генерация CSRF токена, если его еще нет
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>

    <form action="login_action.php" method="POST">
        <!-- CSRF токен -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label for="email">Электронная почта:</label>
        <input type="email" id="email" name="email" placeholder="example@domain.com" required><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" placeholder="Введите пароль" required><br>

        <button type="submit">Войти</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
