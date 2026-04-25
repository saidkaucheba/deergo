<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$base = 'deergo';

$conn = mysqli_connect($host, $user, $pass, $base, 3306);

if (!$conn) {
    die('Ошибка подключения к БД: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>