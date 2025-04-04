<?php
// dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

echo "Добро пожаловать, " . $_SESSION['username'];
?>

<a href="logout.php">Выход</a>