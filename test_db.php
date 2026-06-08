<?php
$host = 'localhost';
$user = 'orb7706769_mysql';
$password = 'vCRu5qo/';
$dbname = 'orb7706769_db';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
} else {
    echo "Подключение успешно!";
}
$conn->close();
?>