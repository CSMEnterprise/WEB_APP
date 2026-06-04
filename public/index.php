<?php

use App\Core\FrontController;
use App\Foundation\FDataBase;

session_start();


require_once __DIR__ . '/../vendor/autoload.php';

$dbConfigPath = __DIR__ . '/../src/config/db.php';
if (!is_file($dbConfigPath)) {
    http_response_code(500);
    die('Configurazione database locale mancante. Crea src/config/db.php con le credenziali MySQL del tuo ambiente.');
}

require_once $dbConfigPath;

FDataBase::init($pdo);

(new FrontController())->handle();
