<?php
// Здесь мы показываем данные профиля пользователя.
include '../includes/header.php';
?>

<main>
    <h1>Мой профиль</h1>
    <p>ФИО: <?php echo $_SESSION['user_name']; ?></p>
    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    <p><a href="edit_profile.php">Редактировать профиль</a></p>
</main>

<?php include '../includes/footer.php'; ?>
