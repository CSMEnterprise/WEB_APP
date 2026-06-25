<?php

/*
|--------------------------------------------------------------------------
| Configurazione database NerdVault
|--------------------------------------------------------------------------
| Copiare questo file in src/config/db.php e inserire le credenziali MySQL
| del proprio ambiente locale.
|
| I valori possono anche essere sovrascritti tramite variabili di ambiente:
| DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD e APP_DEBUG.
|--------------------------------------------------------------------------
*/

$debugValue = getenv('APP_DEBUG');
if (!defined('APP_DEBUG')) {
    define(
        'APP_DEBUG',
        $debugValue === false
            ? true
            : filter_var($debugValue, FILTER_VALIDATE_BOOL)
    );
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'nerdvault';
$username = getenv('DB_USERNAME') ?: 'root';
$passwordValue = getenv('DB_PASSWORD');
$password = $passwordValue === false ? '' : $passwordValue;
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die('Errore connessione database: ' . $e->getMessage());
    }

    die('Errore connessione database.');
}
