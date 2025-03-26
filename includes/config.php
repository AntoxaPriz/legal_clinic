<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'clinic_db');

function getDbConnection() {
    $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($connection->connect_error) {
        die("Ошибка подключения: " . $connection->connect_error);
    }
    return $connection;
}
?>