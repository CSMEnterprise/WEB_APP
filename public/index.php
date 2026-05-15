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
require_once __DIR__ . '/../src/controllers/WishlistController.php';
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
    'scegli-registrazione' => 'register',

    'register-normal' => 'register-user',
    'register-utente' => 'register-user',
    'registrazione-utente' => 'register-user',
    'utente-create' => 'register-user',
    'crea-utente' => 'register-user',

    'registrazione-post' => 'register-user-post',
    'registrazione-store' => 'register-user-post',
    'registrati-post' => 'register-user-post',
    'signup-post' => 'register-user-post',
    'utente-store' => 'register-user-post',
    'utente-register' => 'register-user-post',
    'register-store' => 'register-user-post',
    'register-post' => 'register-user-post',
    'crea-utente-post' => 'register-user-post',

    'utente-profilo' => 'profilo',
    'profile' => 'profilo',
    'profilo-attivi' => 'profilo-annunci-attivi',
    'miei-annunci-attivi' => 'profilo-annunci-attivi',
    'profilo-venduti' => 'profilo-annunci-venduti',
    'miei-annunci-venduti' => 'profilo-annunci-venduti',

    'preferiti' => 'wishlist',
    'preferito' => 'wishlist',
    'wishlist-list' => 'wishlist',
    'wishlist-add-post' => 'wishlist-add',
    'preferiti-add' => 'wishlist-add',
    'preferito-add' => 'wishlist-add',
    'preferiti-remove' => 'wishlist-remove',
    'preferito-remove' => 'wishlist-remove',

    'paypal' => 'paypal-placeholder',
    'paypal-checkout' => 'paypal-placeholder',
    'paypal-sandbox' => 'paypal-placeholder',
    'paypal-sim' => 'paypal-placeholder',
    'pagamento-paypal' => 'paypal-placeholder',
    'paypal-annulla' => 'paypal-cancel',
    'pagamento-annulla' => 'paypal-cancel',

    'business-profilo' => 'business',
    'profilo-business' => 'business',
    'account-business' => 'business',

    'business-new' => 'business-create',
    'business-form' => 'business-create',
    'business-register' => 'register-business',
    'business-crea' => 'business-create',
    'registrazione-business' => 'register-business',
    'registrati-business' => 'register-business',
    'signup-business' => 'register-business',
    'register-business-store' => 'register-business-post',
    'business-signup-post' => 'register-business-post',
    'registrazione-business-post' => 'register-business-post',
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
            require_once __DIR__ . '/../src/services/AnnuncioService.php';
            $homeAnnuncioService = new AnnuncioService($pdo);
            $homeTitoloAnnunci = 'Annunci scelti per te';

            if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin'])) {
                $homeAnnunci = $homeAnnuncioService->getAnnunciPerInteressiUtente(currentUserId());
            } else {
                $homeTitoloAnnunci = 'Annunci in evidenza';
                $homeAnnunci = $homeAnnuncioService->getAnnunciCasuali();
            }

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

        case 'register-user':
        case 'registrazione-utente':
            requireGuest();
            (new UtenteController($pdo))->showRegisterUser();
            break;

        case 'register-user-post':
        case 'register-post':
        case 'registrazione-post':
            requireGuest();
            (new UtenteController($pdo))->register($_POST);
            break;

        case 'register-business':
        case 'registrazione-business':
            requireGuest();
            (new UtenteController($pdo))->showRegisterBusiness();
            break;

        case 'register-business-post':
        case 'registrazione-business-post':
            requireGuest();
            (new UtenteController($pdo))->registerBusiness($_POST);
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
        case 'profilo-annunci-attivi':
            requireAuth();
            if (!empty($_SESSION['is_admin'])) {
                (new AdminController($pdo))->dashboard(currentUserId());
                break;
            }
            (new UtenteController($pdo))->profilo(currentUserId(), 'attivo');
            break;

        case 'profilo-annunci-venduti':
            requireAuth();
            denyAdmin();
            (new UtenteController($pdo))->profilo(currentUserId(), 'venduto');
            break;

        case 'profilo-propic-store':
            requireAuth();
            denyAdmin();
            (new UtenteController($pdo))->aggiornaFotoProfilo($_FILES, currentUserId());
            break;

        case 'profilo-indirizzo-store':
            requireAuth();
            denyAdmin();
            (new UtenteController($pdo))->salvaIndirizzoSpedizione($_POST, currentUserId());
            break;
        /*
        |--------------------------------------------------------------------------
        | Annunci
        |--------------------------------------------------------------------------
        */

        case 'annuncio-create':
            requireAuth();
            denyAdmin();
            (new AnnuncioController($pdo))->formCreazione();
            break;

        case 'annuncio-store':
            requireAuth();
            denyAdmin();
            (new AnnuncioController($pdo))->crea($_POST, currentUserId(), $_FILES);
            break;

        case 'annuncio-delete':
            requireAuth();
            denyAdmin();
            (new AnnuncioController($pdo))->elimina((int) ($_GET['id'] ?? 0), currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Carrello
        |--------------------------------------------------------------------------
        */

        case 'carrello':
            requireAuth();
            denyAdmin();
            (new CarrelloController($pdo))->lista(currentUserId());
            break;

        case 'carrello-add':
            requireAuth();
            denyAdmin();
            (new CarrelloController($pdo))->aggiungi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'carrello-remove':
            requireAuth();
            denyAdmin();
            (new CarrelloController($pdo))->rimuovi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'carrello-clear':
            requireAuth();
            denyAdmin();
            (new CarrelloController($pdo))->svuota(currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Wishlist
        |--------------------------------------------------------------------------
        */

        case 'wishlist':
        case 'preferiti':
            requireAuth();
            denyAdmin();
            (new WishlistController($pdo))->lista(currentUserId());
            break;

        case 'wishlist-add':
            requireAuth();
            denyAdmin();
            (new WishlistController($pdo))->aggiungi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'wishlist-remove':
            requireAuth();
            denyAdmin();
            (new WishlistController($pdo))->rimuovi(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'wishlist-clear':
            requireAuth();
            denyAdmin();
            (new WishlistController($pdo))->svuota(currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Pagamenti
        |--------------------------------------------------------------------------
        */

        case 'checkout':
            requireAuth();
            denyAdmin();
            (new PagamentoController($pdo))->checkout(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'paypal-placeholder':
            requireAuth();
            denyAdmin();
            (new PagamentoController($pdo))->paypalPlaceholder(currentUserId(), (int) ($_GET['id'] ?? 0));
            break;

        case 'paypal-cancel':
            requireAuth();
            denyAdmin();
            (new PagamentoController($pdo))->paypalCancel();
            break;

        case 'pagamento-conferma':
            requireAuth();
            denyAdmin();
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
            denyAdmin();
            (new BusinessController($pdo))->dashboard(currentUserId());
            break;

        case 'business-create':
            requireAuth();
            denyAdmin();
            (new BusinessController($pdo))->formCreazione();
            break;

        case 'business-store':
            requireAuth();
            denyAdmin();
            (new BusinessController($pdo))->creaAccount($_POST, currentUserId());
            break;

        case 'business-ordini':
            requireAuth();
            denyAdmin();
            requireBusiness($pdo);
            (new BusinessController($pdo))->ordini(currentUserId());
            break;

        case 'business-indirizzo-store':
            requireAuth();
            denyAdmin();
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

        case 'feedback-create':
            requireAuth();
            denyAdmin();
            (new FeedbackController($pdo))->form((int) ($_GET['id_pagamento'] ?? 0), currentUserId());
            break;

        case 'feedback-store':
            requireAuth();
            denyAdmin();
            (new FeedbackController($pdo))->crea($_POST, currentUserId());
            break;

        case 'feedback-venditore':
            (new FeedbackController($pdo))->listaVenditore((int) ($_GET['id'] ?? 0));
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
            (new SegnalazioneController($pdo))->chiudi((int) ($_GET['id'] ?? 0), currentUserId());
            break;

        case 'segnalazione-delete':
            requireAdmin();
            (new SegnalazioneController($pdo))->elimina((int) ($_GET['id'] ?? 0), currentUserId());
            break;


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        case 'admin':
            requireAdmin();
            (new AdminController($pdo))->dashboard(currentUserId());
            break;

        case 'admin-dashboard':
            requireAdminLivello2();
            (new AdminController($pdo))->dashboardModerazione($_GET);
            break;

        case 'admin-utenti':
            requireAdmin();
            (new AdminController($pdo))->utenti($_GET);
            break;

        case 'admin-banna-utente':
            requireAdmin();
            (new AdminController($pdo))->bannaUtente((int) ($_GET['id'] ?? 0), currentUserId());
            break;

        case 'admin-sblocca-utente':
            requireAdmin();
            (new AdminController($pdo))->sbloccaUtente((int) ($_GET['id'] ?? 0), currentUserId());
            break;

        case 'admin-banna-admin':
            requireAdminLivello2();
            (new AdminController($pdo))->bannaAdmin((int) ($_GET['id'] ?? 0), currentUserId());
            break;

        case 'admin-sblocca-admin':
            requireAdminLivello2();
            (new AdminController($pdo))->sbloccaAdmin((int) ($_GET['id'] ?? 0), currentUserId());
            break;

        case 'admin-segnalazioni':
            requireAdmin();
            (new AdminController($pdo))->segnalazioni($_GET);
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

    /*
     * Durante lo sviluppo puoi mostrare l'errore reale.
     * In produzione è meglio lasciare solo il messaggio generico.
     */
    if (defined('APP_DEBUG') && APP_DEBUG === true) {
        $errore = $e->getMessage();
    }

    require __DIR__ . '/../src/views/errors/400.php';
}
