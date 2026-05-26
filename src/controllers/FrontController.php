<?php

namespace App\Controllers;

use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use PDO;
use Throwable;
use function App\Middleware\currentUserId;
use function App\Middleware\denyAdmin;
use function App\Middleware\denyBusiness;
use function App\Middleware\requireAdmin;
use function App\Middleware\requireAdminLivello2;
use function App\Middleware\requireAuth;
use function App\Middleware\requireBusiness;
use function App\Middleware\requireGuest;

class FrontController extends BaseController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $GLOBALS['pdo'] = $pdo;
        FDataBase::init($pdo);
    }

    public function handle(): void
    {
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
                    $q          = trim($_GET['q'] ?? '');
                    $idCategoria = (int) ($_GET['id_categoria'] ?? 0);
                    $prezzoMin = isset($_GET['prezzo_min']) && $_GET['prezzo_min'] !== '' ? max(0, (float) $_GET['prezzo_min']) : null;
                    $prezzoMax = isset($_GET['prezzo_max']) && $_GET['prezzo_max'] !== '' ? max(0, (float) $_GET['prezzo_max']) : null;
                    $ordinamento = (string) ($_GET['ordinamento'] ?? 'data_desc');
                    $ordinamentiValidi = ['data_desc', 'data_asc', 'prezzo_asc', 'prezzo_desc'];
                    if (!in_array($ordinamento, $ordinamentiValidi, true)) {
                        $ordinamento = 'data_desc';
                    }
                    if ($prezzoMin !== null && $prezzoMax !== null && $prezzoMin > $prezzoMax) {
                        [$prezzoMin, $prezzoMax] = [$prezzoMax, $prezzoMin];
                    }
                    $hasFiltriAvanzati = $prezzoMin !== null || $prezzoMax !== null || $ordinamento !== 'data_desc';
                    $annunciPerPagina = 12;
                    $paginaCorrente = max(1, (int) ($_GET['page'] ?? 1));
                    $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
                    $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);
                    $excludeHomeUserId = $isRegularUser ? currentUserId() : null;
                    $wishlistIds = [];
                    $carrelloIds = [];
                    $utenti      = [];
                    $categorie   = $this->entitiesToArrays(FPersistentManager::categorie());

                    if ($q !== '' || $idCategoria > 0 || $hasFiltriAvanzati) {
                        // modalità ricerca
                        $totaleAnnunci     = FPersistentManager::countSearchAnnunci($q, $idCategoria, $prezzoMin, $prezzoMax, $excludeHomeUserId);
                        $totalePagine      = max(1, (int) ceil($totaleAnnunci / $annunciPerPagina));
                        if ($paginaCorrente > $totalePagine) {
                            $paginaCorrente = $totalePagine;
                            $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
                        }
                        $homeAnnunci       = $this->entitiesToArrays(FPersistentManager::searchAnnunci($q, $idCategoria, $prezzoMin, $prezzoMax, $ordinamento, $annunciPerPagina, $offsetAnnunci, $excludeHomeUserId));
                        $utenti            = $q !== '' ? $this->entitiesToArrays(FPersistentManager::searchUtenti($q)) : [];
                        $homeTitoloAnnunci = $q !== '' ? 'Risultati per "' . $q . '"' : 'Risultati ricerca';
                    } else {
                        $totaleAnnunci     = FPersistentManager::countSearchAnnunci('', 0, null, null, $excludeHomeUserId);
                        $totalePagine      = max(1, (int) ceil($totaleAnnunci / $annunciPerPagina));
                        if ($paginaCorrente > $totalePagine) {
                            $paginaCorrente = $totalePagine;
                            $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
                        }
                        $homeAnnunci       = $this->entitiesToArrays(FPersistentManager::searchAnnunci('', 0, null, null, 'data_desc', $annunciPerPagina, $offsetAnnunci, $excludeHomeUserId));
                        $homeTitoloAnnunci = 'Annunci in evidenza';
                    }

                    if ($isRegularUser) {
                        $wishlistIds = FPersistentManager::wishlistIdsByUser(currentUserId());
                        $carrelloIds = FPersistentManager::carrelloAnnuncioIdsByUser(currentUserId());
                    }

                    $prezzoMinValue = $prezzoMin !== null ? (string) $prezzoMin : '';
                    $prezzoMaxValue = $prezzoMax !== null ? (string) $prezzoMax : '';
                    $isRicerca = $q !== '' || $idCategoria > 0 || $hasFiltriAvanzati;
                    $pagination = $this->buildHomePagination($paginaCorrente, $totalePagine);
                    $resetFiltersUrl = $this->buildHomeResetUrl($q, $idCategoria);

                    $this->view('home.tpl', compact(
                        'q',
                        'idCategoria',
                        'prezzoMinValue',
                        'prezzoMaxValue',
                        'ordinamento',
                        'hasFiltriAvanzati',
                        'isRicerca',
                        'homeAnnunci',
                        'utenti',
                        'categorie',
                        'wishlistIds',
                        'carrelloIds',
                        'paginaCorrente',
                        'totalePagine',
                        'totaleAnnunci',
                        'homeTitoloAnnunci',
                        'pagination',
                        'resetFiltersUrl'
                    ), 'Home');
                    break;

                case 'annunci':
                    // Pagina rimossa: reindirizza alla home preservando eventuali parametri di ricerca
                    $qs = http_build_query(array_merge($_GET, ['route' => 'home']));
                    header('Location: index.php?' . $qs);
                    exit;
                case 'annuncio':
                    (new AnnuncioController($this->pdo))->dettaglio((int) ($_GET['id'] ?? 0));
                    break;

                case 'venditore':
                    (new UtenteController($this->pdo))->venditore((int) ($_GET['id'] ?? 0));
                    break;


                /*
                |--------------------------------------------------------------------------
                | Autenticazione
                |--------------------------------------------------------------------------
                */

                case 'login':
                    requireGuest();
                    (new UtenteController($this->pdo))->showLogin();
                    break;

                case 'login-post':
                    requireGuest();
                    (new UtenteController($this->pdo))->login($_POST);
                    break;

                case 'verifica-email-attesa':
                    (new UtenteController($this->pdo))->verificaEmailAttesa();
                    break;

                case 'verifica-email':
                    (new UtenteController($this->pdo))->verificaEmail($_GET['token'] ?? '');
                    break;

                case 'reinvia-verifica':
                    (new UtenteController($this->pdo))->reinviaVerifica($_POST);
                    break;

                case 'recupero-password':
                    requireGuest();
                    (new UtenteController($this->pdo))->showRecuperoPassword();
                    break;

                case 'recupero-password-post':
                    requireGuest();
                    (new UtenteController($this->pdo))->inviaResetPassword($_POST);
                    break;

                case 'reset-password':
                    requireGuest();
                    (new UtenteController($this->pdo))->showResetPassword($_GET['token'] ?? '');
                    break;

                case 'reset-password-post':
                    requireGuest();
                    (new UtenteController($this->pdo))->resetPassword($_POST);
                    break;

                case 'register':
                case 'registrazione':
                    requireGuest();
                    (new UtenteController($this->pdo))->showRegister();
                    break;

                case 'register-user':
                case 'registrazione-utente':
                    requireGuest();
                    (new UtenteController($this->pdo))->showRegisterUser();
                    break;

                case 'register-user-post':
                case 'register-post':
                case 'registrazione-post':
                    requireGuest();
                    (new UtenteController($this->pdo))->register($_POST);
                    break;

                case 'register-business':
                case 'registrazione-business':
                    requireGuest();
                    (new UtenteController($this->pdo))->showRegisterBusiness();
                    break;

                case 'register-business-post':
                case 'registrazione-business-post':
                    requireGuest();
                    (new UtenteController($this->pdo))->registerBusiness($_POST);
                    break;

                case 'logout':
                    requireAuth();
                    (new UtenteController($this->pdo))->logout();
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
                        (new AdminController($this->pdo))->dashboard(currentUserId());
                        break;
                    }
                    (new UtenteController($this->pdo))->profilo(currentUserId(), 'attivo');
                    break;

                case 'profilo-annunci-venduti':
                    requireAuth();
                    denyAdmin();
                    (new UtenteController($this->pdo))->profilo(currentUserId(), 'venduto');
                    break;

                case 'profilo-propic-store':
                    requireAuth();
                    denyAdmin();
                    (new UtenteController($this->pdo))->aggiornaFotoProfilo($_FILES, currentUserId());
                    break;

                case 'profilo-update':
                    requireAuth();
                    denyAdmin();
                    (new UtenteController($this->pdo))->aggiornaProfiloUtente($_POST, currentUserId());
                    break;

                case 'profilo-password':
                    requireAuth();
                    denyAdmin();
                    (new UtenteController($this->pdo))->cambiaPassword($_POST, currentUserId());
                    break;

                case 'profilo-indirizzo-store':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new UtenteController($this->pdo))->salvaIndirizzoSpedizione($_POST, currentUserId());
                    break;

                case 'profilo-indirizzo-default':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new UtenteController($this->pdo))->impostaIndirizzoPredefinito((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'profilo-indirizzo-edit':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new UtenteController($this->pdo))->showModificaIndirizzo((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'profilo-indirizzo-update':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new UtenteController($this->pdo))->aggiornaIndirizzo($_POST, currentUserId());
                    break;

                case 'profilo-indirizzo-delete':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new UtenteController($this->pdo))->eliminaIndirizzo((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                /*
                |--------------------------------------------------------------------------
                | Annunci
                |--------------------------------------------------------------------------
                */

                case 'annuncio-create':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->formCreazione();
                    break;

                case 'annuncio-store':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->crea($_POST, currentUserId(), $_FILES);
                    break;

                case 'annuncio-edit':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->formModifica((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'annuncio-update':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->aggiorna($_POST, currentUserId(), $_FILES);
                    break;

                case 'annuncio-image-delete':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->eliminaImmagine($_POST, currentUserId());
                    break;

                case 'annuncio-delete':
                    requireAuth();
                    denyAdmin();
                    (new AnnuncioController($this->pdo))->elimina((int) ($_GET['id'] ?? 0), currentUserId());
                    break;


                /*
                |--------------------------------------------------------------------------
                | Carrello
                |--------------------------------------------------------------------------
                */

                case 'carrello':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new CarrelloController($this->pdo))->lista(currentUserId());
                    break;

                case 'carrello-add':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new CarrelloController($this->pdo))->aggiungi(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'carrello-remove':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new CarrelloController($this->pdo))->rimuovi(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'carrello-clear':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new CarrelloController($this->pdo))->svuota(currentUserId());
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
                    denyBusiness();
                    (new WishlistController($this->pdo))->lista(currentUserId());
                    break;

                case 'wishlist-add':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new WishlistController($this->pdo))->aggiungi(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'wishlist-remove':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new WishlistController($this->pdo))->rimuovi(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'wishlist-toggle':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new WishlistController($this->pdo))->toggle(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'wishlist-clear':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new WishlistController($this->pdo))->svuota(currentUserId());
                    break;


                /*
                |--------------------------------------------------------------------------
                | Pagamenti
                |--------------------------------------------------------------------------
                */

                case 'checkout':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->checkout(currentUserId(), (int) ($_GET['id'] ?? 0));
                    break;

                case 'checkout-carrello':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->checkoutCarrello(currentUserId());
                    break;

                case 'paypal-placeholder-carrello':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->paypalPlaceholderCarrello(currentUserId(), (int) ($_GET['id_indirizzo'] ?? 0));
                    break;

                case 'pagamento-conferma-carrello':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->confermaCarrello($_POST, currentUserId());
                    break;

                case 'paypal-placeholder':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->paypalPlaceholder(
                        currentUserId(),
                        (int) ($_GET['id'] ?? $_POST['id_annuncio'] ?? 0),
                        (int) ($_POST['id_indirizzo'] ?? $_GET['id_indirizzo'] ?? 0)
                    );
                    break;

                case 'paypal-cancel':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->paypalCancel();
                    break;

                case 'pagamento-conferma':
                    requireAuth();
                    denyAdmin();
                    denyBusiness();
                    (new PagamentoController($this->pdo))->conferma($_POST, currentUserId());
                    break;

                case 'pagamento-esito':
                    requireAuth();
                    (new PagamentoController($this->pdo))->esito();
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
                    (new BusinessController($this->pdo))->dashboard(currentUserId());
                    break;

                case 'business-create':
                    requireAuth();
                    denyAdmin();
                    (new BusinessController($this->pdo))->formCreazione();
                    break;

                case 'business-store':
                    requireAuth();
                    denyAdmin();
                    (new BusinessController($this->pdo))->creaAccount($_POST, currentUserId());
                    break;

                case 'business-ordini':
                    requireAuth();
                    denyAdmin();
                    requireBusiness($this->pdo);
                    (new BusinessController($this->pdo))->ordini(currentUserId());
                    break;

                case 'business-indirizzo-store':
                    requireAuth();
                    denyAdmin();
                    requireBusiness($this->pdo);
                    (new BusinessController($this->pdo))->salvaIndirizzo($_POST, currentUserId());
                    break;


                /*
                |--------------------------------------------------------------------------
                | Feedback
                |--------------------------------------------------------------------------
                */

                case 'feedback':
                    requireAuth();
                    (new FeedbackController($this->pdo))->lista(currentUserId());
                    break;

                case 'feedback-create':
                    requireAuth();
                    denyAdmin();
                    (new FeedbackController($this->pdo))->form((int) ($_GET['id_pagamento'] ?? 0), currentUserId());
                    break;

                case 'feedback-store':
                    requireAuth();
                    denyAdmin();
                    (new FeedbackController($this->pdo))->crea($_POST, currentUserId());
                    break;

                case 'feedback-venditore':
                    (new FeedbackController($this->pdo))->listaVenditore((int) ($_GET['id'] ?? 0));
                    break;


                /*
                |--------------------------------------------------------------------------
                | Segnalazioni
                |--------------------------------------------------------------------------
                */

                case 'segnalazione-create':
                    requireAuth();
                    (new SegnalazioneController($this->pdo))->form();
                    break;

                case 'segnalazione-store':
                    requireAuth();
                    (new SegnalazioneController($this->pdo))->crea($_POST, currentUserId());
                    break;

                case 'segnalazione-close':
                    requireAdmin();
                    (new SegnalazioneController($this->pdo))->chiudi((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'segnalazione-delete':
                    requireAdmin();
                    (new SegnalazioneController($this->pdo))->elimina((int) ($_GET['id'] ?? 0), currentUserId());
                    break;


                /*
                |--------------------------------------------------------------------------
                | Admin
                |--------------------------------------------------------------------------
                */

                case 'admin':
                    requireAdmin();
                    (new AdminController($this->pdo))->dashboard(currentUserId());
                    break;

                case 'admin-dashboard':
                    requireAdminLivello2();
                    (new AdminController($this->pdo))->dashboardModerazione($_GET);
                    break;

                case 'admin-utenti':
                    requireAdmin();
                    (new AdminController($this->pdo))->utenti($_GET);
                    break;

                case 'admin-banna-utente':
                    requireAdmin();
                    (new AdminController($this->pdo))->bannaUtente((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'admin-sblocca-utente':
                    requireAdmin();
                    (new AdminController($this->pdo))->sbloccaUtente((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'admin-banna-admin':
                    requireAdminLivello2();
                    (new AdminController($this->pdo))->bannaAdmin((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'admin-sblocca-admin':
                    requireAdminLivello2();
                    (new AdminController($this->pdo))->sbloccaAdmin((int) ($_GET['id'] ?? 0), currentUserId());
                    break;

                case 'admin-segnalazioni':
                    requireAdmin();
                    (new AdminController($this->pdo))->segnalazioni($_GET);
                    break;


                /*
                |--------------------------------------------------------------------------
                | Pagine Legali
                |--------------------------------------------------------------------------
                */

                case 'privacy':
                    $this->view('legale/privacy.tpl', [], 'Privacy Policy');
                    break;

                case 'termini':
                    $this->view('legale/termini.tpl', [], 'Termini di servizio');
                    break;

                case 'cookie':
                    $this->view('legale/cookie.tpl', [], 'Cookie');
                    break;

                /*
                |--------------------------------------------------------------------------
                | Rotta non trovata
                |--------------------------------------------------------------------------
                */

                default:
                    $this->renderError('La pagina richiesta non esiste.', 404);
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

            $this->renderError($errore, 500);
        }

    }

    private function buildHomePagination(int $paginaCorrente, int $totalePagine): array
    {
        $buildPageUrl = static function (int $page): string {
            $params = $_GET;
            $params['route'] = 'home';
            $params['page'] = $page;

            foreach ($params as $key => $value) {
                if ($value === '' || $value === null) {
                    unset($params[$key]);
                }
            }

            return 'index.php?' . http_build_query($params);
        };

        $paginaDa = max(1, $paginaCorrente - 2);
        $paginaA = min($totalePagine, $paginaCorrente + 2);
        $pages = [];

        if ($paginaDa > 1) {
            $pages[] = ['number' => 1, 'url' => $buildPageUrl(1), 'active' => false];
            if ($paginaDa > 2) {
                $pages[] = ['ellipsis' => true];
            }
        }

        for ($pagina = $paginaDa; $pagina <= $paginaA; $pagina++) {
            $pages[] = [
                'number' => $pagina,
                'url' => $buildPageUrl($pagina),
                'active' => $pagina === $paginaCorrente,
            ];
        }

        if ($paginaA < $totalePagine) {
            if ($paginaA < $totalePagine - 1) {
                $pages[] = ['ellipsis' => true];
            }
            $pages[] = ['number' => $totalePagine, 'url' => $buildPageUrl($totalePagine), 'active' => false];
        }

        return [
            'show' => $totalePagine > 1,
            'prev' => $paginaCorrente > 1 ? $buildPageUrl($paginaCorrente - 1) : '',
            'next' => $paginaCorrente < $totalePagine ? $buildPageUrl($paginaCorrente + 1) : '',
            'pages' => $pages,
        ];
    }

    private function buildHomeResetUrl(string $q, int $idCategoria): string
    {
        $params = ['route' => 'home'];

        if ($q !== '') {
            $params['q'] = $q;
        }

        if ($idCategoria > 0) {
            $params['id_categoria'] = $idCategoria;
        }

        return 'index.php?' . http_build_query($params);
    }
}
