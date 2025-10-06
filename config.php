<?php
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'user_form_db_zasim');
define('DB_CHARSET', 'utf8mb4');

function getDatabaseConnection() {
    $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($connection->connect_error) {
        error_log("Ошибка подключения к базе данных: " . $connection->connect_error);
        return false;
    }
    
    $connection->set_charset(DB_CHARSET);
    return $connection;
}

function closeDatabaseConnection($connection) {
    if ($connection) {
        $connection->close();
    }
}
?>