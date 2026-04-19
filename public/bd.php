<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$base = 'deergo';

$conn = mysqli_connect($host, $user, $pass, $base);

if (!$conn) {
    die('Ошибка подключения к БД: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>