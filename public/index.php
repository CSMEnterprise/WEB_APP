<?php

session_start();

require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/config/db.php';

require_once __DIR__ . '/../src/middleware/auth.php';
require_once __DIR__ . '/../src/middleware/admin.php';
require_once __DIR__ . '/../src/middleware/business.php';
require_once __DIR__ . '/../src/middleware/guest.php';

require_once __DIR__ . '/../src/controllers/UtenteController.php';
require_once __DIR__ . '/../src/controllers/AnnuncioController.php';
require_once __DIR__ . '/../src/controllers/CarrelloController.php';
require_once __DIR__ . '/../src/controllers/PagamentoController.php';
require_once __DIR__ . '/../src/controllers/BusinessController.php';
require_once __DIR__ . '/../src/controllers/FeedbackController.php';
require_once __DIR__ . '/../src/controllers/SegnalazioneController.php';
require_once __DIR__ . '/../src/controllers/AdminController.php';

/*
 * Recupero rotta più robusto:
 * - da query string: index.php?route=...
 * - da POST hidden: <input name="route" ...>
 * - da PATH_INFO: index.php/profilo
 * Inoltre normalizza underscore/spazi in trattini e gestisce alias vecchi.
 */
$route = $_GET['route'] ?? $_POST['route'] ?? '';

if ($route === '' && !empty($_SERVER['PATH_INFO'])) {
    $route = trim((string) $_SERVER['PATH_INFO'], '/');
}

$route = strtolower(trim((string) $route));
$route = str_replace(['_', ' '], '-', $route);
$route = trim($route, '/');

if ($route === '') {
    $route = 'home';
}

$routeAliases = [
    'index' => 'home',
    'homepage' => 'home',

    'registrazione' => 'register',
    'registrati' => 'register',
    'signup' => 'register',
    'utente-create' => 'register',
    'crea-utente' => 'register',

    'registrazione-post' => 'register-post',
    'registrazione-store' => 'register-post',
    'registrati-post' => 'register-post',
    'signup-post' => 'register-post',
    'utente-store' => 'register-post',
    'utente-register' => 'register-post',
    'register-store' => 'register-post',
    'crea-utente-post' => 'register-post',

    'utente-profilo' => 'profilo',
    'profile' => 'profilo',

    'business-profilo' => 'business',
    'profilo-business' => 'business',
    'account-business' => 'business',

    'business-new' => 'business-create',
    'business-form' => 'business-create',
    'business-register' => 'business-create',
    'business-crea' => 'business-create',
    'crea-business' => 'business-create',

    'business-save' => 'business-store',
    'business-register-post' => 'business-store',
    'business-crea-post' => 'business-store',
    'business-create-post' => 'business-store',
    'business-store-post' => 'business-store',
    'account-business-store' => 'business-store',
];

$route = $routeAliases[$route] ?? $route;

try {
    switch ($route) {

        /*
        |--------------------------------------------------------------------------
        | Pagine pubbliche
        |--------------------------------------------------------------------------
        */

        case 'home':
            require __DIR__ . '/../src/views/home.php';
            break;

        case 'annunci':
            (new AnnuncioController($pdo))->lista();
            break;
        case 'annuncio':
            (new AnnuncioController($pdo))->dettaglio((int) ($_GET['id'] ?? 0));
            break;


        /*
        |--------------------------------------------------------------------------
        | Autenticazione
        |--------------------------------------------------------------------------
        */

        case 'login':
            requireGuest();
            (new UtenteController($pdo))->showLogin();
            break;

        case 'login-post':
            requireGuest();
            (new UtenteController($pdo))->login($_POST);
            break;

        case 'register':
        case 'registrazione':
            requireGuest();
            (new UtenteController($pdo))->showRegister();
            break;

        case 'register-post':
        case 'registrazione-post':
            requireGuest();
            (new UtenteController($pdo))->register($_POST);
            break;

        case 'logout':
            requireAuth();
            (new UtenteController($pdo))->logout();
            break;


        /*
        |--------------------------------------------------------------------------
        | Utente autenticato
        |--------------------------------------------------------------------------
        */

        case 'profilo':
        case 'utente-profilo':
            requireAuth();
            (new UtenteController($pdo))->profilo(currentUserId());
            break;

        case 'profilo-indirizzo-store':
            requireAuth();
            (new UtenteController($pdo))->salvaIndirizzoSpedizione($_POST, currentUserId());
            break;

        /*
        |--------------------------------------------------------------------------
        | Annunci
        |--------------------------------------------------------------------------
        */

        case 'annuncio-create':
            requireAuth();
            (new AnnuncioController($pdo))->formCreazione();
            break;

        case 'annuncio-store':
            requireAuth();
            (new AnnuncioController($pdo))->crea($_POST, currentUserId(), $_FILES);
            break;

        case 'annuncio-delete':
            requireAuth();
            (new AnnuncioController($pdo))->elimina((int) ($_GET['id'] ?? 0), currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Carrello
        |--------------------------------------------------------------------------
        */

        case 'carrello':
            requireAuth();
            (new CarrelloController($pdo))->lista(currentUserId());
            break;

        case 'carrello-add':
            requireAuth();
            (new CarrelloController($pdo))->aggiungi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'carrello-remove':
            requireAuth();
            (new CarrelloController($pdo))->rimuovi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'carrello-clear':
            requireAuth();
            (new CarrelloController($pdo))->svuota(currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Pagamenti
        |--------------------------------------------------------------------------
        */

        case 'checkout':
            requireAuth();
            (new PagamentoController($pdo))->checkout(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'pagamento-conferma':
            requireAuth();
            (new PagamentoController($pdo))->conferma($_POST, currentUserId());
            break;

        case 'pagamento-esito':
            requireAuth();
            (new PagamentoController($pdo))->esito();
            break;


        /*
        |--------------------------------------------------------------------------
        | Business
        |--------------------------------------------------------------------------
        */

        case 'business':
        case 'business-profilo':
            requireAuth();
            (new BusinessController($pdo))->dashboard(currentUserId());
            break;

        case 'business-create':
            requireAuth();
            (new BusinessController($pdo))->formCreazione();
            break;

        case 'business-store':
            requireAuth();
            (new BusinessController($pdo))->creaAccount($_POST, currentUserId());
            break;

        case 'business-ordini':
            requireAuth();
            requireBusiness($pdo);
            (new BusinessController($pdo))->ordini(currentUserId());
            break;

        case 'business-indirizzo-store':
            requireAuth();
            requireBusiness($pdo);
            (new BusinessController($pdo))->salvaIndirizzo($_POST, currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Feedback
        |--------------------------------------------------------------------------
        */

        case 'feedback':
            requireAuth();
            (new FeedbackController($pdo))->lista(currentUserId());
            break;

        case 'feedback-store':
            requireAuth();
            (new FeedbackController($pdo))->crea($_POST, currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Segnalazioni
        |--------------------------------------------------------------------------
        */

        case 'segnalazione-create':
            requireAuth();
            (new SegnalazioneController($pdo))->form();
            break;

        case 'segnalazione-store':
            requireAuth();
            (new SegnalazioneController($pdo))->crea($_POST, currentUserId());
            break;

        case 'segnalazione-close':
            requireAdmin();
            (new SegnalazioneController($pdo))->chiudi((int) ($_GET['id'] ?? 0));
            break;

        case 'segnalazione-delete':
            requireAdmin();
            (new SegnalazioneController($pdo))->elimina((int) ($_GET['id'] ?? 0));
            break;


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        case 'admin':
            requireAdmin();
            (new AdminController($pdo))->dashboard();
            break;

        case 'admin-utenti':
            requireAdmin();
            (new AdminController($pdo))->utenti();
            break;

        case 'admin-banna-utente':
            requireAdmin();
            (new AdminController($pdo))->bannaUtente((int) ($_GET['id'] ?? 0));
            break;

        case 'admin-sblocca-utente':
            requireAdmin();
            (new AdminController($pdo))->sbloccaUtente((int) ($_GET['id'] ?? 0));
            break;

        case 'admin-segnalazioni':
            requireAdmin();
            (new AdminController($pdo))->segnalazioni();
            break;


        /*
        |--------------------------------------------------------------------------
        | Rotta non trovata
        |--------------------------------------------------------------------------
        */

        default:
            http_response_code(404);
            require __DIR__ . '/../src/views/errors/404.php';
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);

    $errore = 'Errore interno del server.';

    if (defined('APP_DEBUG') && APP_DEBUG === true) {
        $errore = $e->getMessage();
    }

    require __DIR__ . '/../src/views/errors/400.php';
}
