<?php

/*
|--------------------------------------------------------------------------
| Configurazione database NerdVault
|--------------------------------------------------------------------------
| Questo file crea la connessione PDO e rende disponibile la variabile $pdo.
| Viene incluso da public/index.php con:
|
| require_once __DIR__ . '/../src/config/db.php';
|--------------------------------------------------------------------------
*/

define('APP_DEBUG', true);

$host = 'localhost';
$port = '3306';
$dbname = 'nerdvault';
$username = 'root';
$password = '';

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG === true) {
        die('Errore connessione database: ' . $e->getMessage());
    }

    die('Errore connessione database.');
}
