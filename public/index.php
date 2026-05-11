<?php

session_start();

/*
|--------------------------------------------------------------------------
| File di configurazione e middleware
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/db.php';

require_once __DIR__ . '/../src/middleware/auth.php';
require_once __DIR__ . '/../src/middleware/admin.php';
require_once __DIR__ . '/../src/middleware/business.php';

/*
|--------------------------------------------------------------------------
| Funzione helper per caricare i controller
|--------------------------------------------------------------------------
*/

function loadController(string $controllerName): void
{
    require_once __DIR__ . '/../src/controllers/' . $controllerName . '.php';
}

/*
|--------------------------------------------------------------------------
| Router principale
|--------------------------------------------------------------------------
*/

$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'home':
        echo "Homepage NerdVault";
        break;

    case 'login':
        loadController('UtenteController');

        $controller = new UtenteController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login($_POST);
        } else {
            require __DIR__ . '/../src/views/utenti/login.php';
        }

        break;

    case 'register':
        loadController('UtenteController');

        $controller = new UtenteController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register($_POST);
        } else {
            require __DIR__ . '/../src/views/utenti/registrazione.php';
        }

        break;

    case 'profilo':
        checkAuth();

        loadController('UtenteController');

        $controller = new UtenteController();
        $controller->profilo();

        break;

    case 'annunci':
        loadController('AnnuncioController');

        $controller = new AnnuncioController();
        $controller->lista();

        break;

    case 'annuncio':
        loadController('AnnuncioController');

        $idAnnuncio = $_GET['id'] ?? null;

        if ($idAnnuncio === null) {
            http_response_code(400);
            echo "Errore: ID annuncio mancante";
            break;
        }

        $controller = new AnnuncioController();
        $controller->dettaglio($idAnnuncio);

        break;

    case 'carrello_add':
        checkAuth();

        loadController('CarrelloController');

        $idAnnuncio = $_GET['id'] ?? null;

        if ($idAnnuncio === null) {
            http_response_code(400);
            echo "Errore: ID annuncio mancante";
            break;
        }

        $controller = new CarrelloController();
        $controller->aggiungi($_SESSION['user_id'], $idAnnuncio);

        break;

    case 'carrello':
        checkAuth();

        loadController('CarrelloController');

        $controller = new CarrelloController();
        $controller->lista($_SESSION['user_id']);

        break;

    case 'pagamento':
        checkAuth();

        loadController('PagamentoController');

        $idAnnuncio = $_GET['id'] ?? null;

        if ($idAnnuncio === null) {
            http_response_code(400);
            echo "Errore: ID annuncio mancante";
            break;
        }

        $controller = new PagamentoController();
        $controller->crea($_SESSION['user_id'], $idAnnuncio);

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