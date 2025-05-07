<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// config.php
$host = 'localhost:3306';
$db   = 'pnwroyet_VyneRoleplay';
$user = 'pnwroyet_Vyne';
$pass = 'b7cc94a17da050203d7860b18384f4c7';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
