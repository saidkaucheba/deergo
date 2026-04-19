<?php
// Подключение к базе данных PostgreSQL
$host = 'localhost';
$dbname = 'deergo';
$user = 'postgres';
$password = '';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к БД: ' . $e->getMessage());
}
?>