<?php
$host = 'localhost';
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=mini_erp;charset=utf8mb4";
$db   = 'mini_erp';
$user = 'root';
$pass = 'SUA_SENHA_AQUI'; // Substitua pela sua senha

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}
