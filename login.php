<?php include 'includes/header.php'; ?>

<main>
    <h1>Вход в личный кабинет</h1>
    <form action="login_action.php" method="POST">
        <label for="email">Электронная почта:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Войти</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
