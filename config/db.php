<?php
$host = 'localhost';
$db   = 'controle_gastos';
$user = 'root';
$pass = ''; // No XAMPP, a senha padrão do root é vazia (sem senha)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Se der erro, mostra na tela (em produção não se faz isso!)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>