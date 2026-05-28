<?php

use App\Core\FrontController;
use App\Foundation\FDataBase;

session_start();


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/middleware/auth.php';
require_once __DIR__ . '/../src/middleware/admin.php';
require_once __DIR__ . '/../src/middleware/business.php';
require_once __DIR__ . '/../src/middleware/guest.php';

FDataBase::init($pdo);

(new FrontController($pdo))->handle();
