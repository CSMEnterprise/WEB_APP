<?php

use App\Controllers\FrontController;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/db.php';

(new FrontController($pdo))->handle();
