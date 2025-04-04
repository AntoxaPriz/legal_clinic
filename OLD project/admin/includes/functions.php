<?php
function getDbConnection() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db = 'clinic';
    $conn = new mysqli($host, $user, $password, $db);

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    return $conn;
}
?>
