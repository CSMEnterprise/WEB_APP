<?php

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/admin.php';
require_once __DIR__ . '/../middleware/business.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'home':
        echo "Homepage NerdVault";
        break;

    case 'login':
        require_once '../controllers/UtenteController.php';
        $controller = new UtenteController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login($_POST);
        } else {
            require '../utenti/login.php';
        }
        break;

    case 'register':
        require_once '../controllers/UtenteController.php';
        $controller = new UtenteController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register($_POST);
        } else {
            require '../utenti/registrazione.php';
        }
        break;

    case 'profilo':
        checkAuth();
        require_once '../controllers/UtenteController.php';
        (new UtenteController())->profilo();
        break;

    case 'annunci':
        require_once '../controllers/AnnuncioController.php';
        (new AnnuncioController())->lista();
        break;

    case 'annuncio':
        require_once '../controllers/AnnuncioController.php';
        (new AnnuncioController())->dettaglio($_GET['id']);
        break;

    case 'carrello_add':
        checkAuth();
        require_once '../controllers/CarrelloController.php';
        (new CarrelloController())->aggiungi($_SESSION['user_id'], $_GET['id']);
        break;

    case 'carrello':
        checkAuth();
        require_once '../controllers/CarrelloController.php';
        (new CarrelloController())->lista($_SESSION['user_id']);
        break;

    case 'pagamento':
        checkAuth();
        require_once '../controllers/PagamentoController.php';
        (new PagamentoController())->crea($_SESSION['user_id'], $_GET['id']);
        break;

    case 'admin_stats':
        checkAdmin();
        echo "Dashboard admin";
        break;

    default:
        http_response_code(404);
        echo "404 - Pagina non trovata";
        break;
}