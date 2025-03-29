<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="register-container">
    <h2>Регистрация</h2>
    <form method="post" action="process_register.php">
        <input type="text" name="username" placeholder="Логин" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <input type="password" name="password_confirm" placeholder="Повторите пароль" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
</div>
</body>
</html>
