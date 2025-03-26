<?php include 'includes/header.php'; ?>

<main>
    <h1>Регистрация</h1>
    <form action="register_action.php" method="POST">
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
