<?php

namespace App\Core;

use App\Controllers\{
    AdminController,
    AnnuncioController,
    BaseController,
    BusinessController,
    CarrelloController,
    FeedbackController,
    HomeController,
    LegalController,
    PagamentoController,
    SegnalazioneController,
    UtenteController,
    WishlistController
};
use PDO;
use ReflectionMethod;
use Throwable;
use function App\Middleware\{
    currentUserId,
    denyAdmin,
    denyBusiness,
    requireAdmin,
    requireAdminLivello2,
    requireAuth,
    requireBusiness,
    requireGuest
};

/**
 * Front Controller: normalizza l'URL e delega a controller/action.
 */
class FrontController extends BaseController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(): void
    {
        try {
            if (!$this->dispatch($this->requestSegments())) {
                $this->renderError('La pagina richiesta non esiste.', 404);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            $message = defined('APP_DEBUG') && APP_DEBUG === true
                ? $e->getMessage()
                : 'Errore interno del server.';

            $this->renderError($message, 500);
        }
    }

    private function dispatch(array $segments): bool
    {
        [$params, $route] = $this->matchRoute($segments);

        if ($route === null) {
            return false;
        }

        foreach ($route['middleware'] ?? [] as $middleware) {
            $middleware();
        }

        $arguments = isset($route['params']) ? $route['params']($params) : $params;
        return $this->callController($route['controller'], $route['action'], $arguments);
    }

    private function routes(): array
    {
        return [
            'home/index' => [
                'controller' => HomeController::class,
                'action' => 'index',
            ],

            'annuncio/list' => [
                'controller' => AnnuncioController::class,
                'action' => 'lista',
            ],
            'annuncio/show' => [
                'urlParams' => true,
                'controller' => AnnuncioController::class,
                'action' => 'dettaglio',
                'params' => fn(array $params) => [$this->id($params)],
            ],
            'annuncio/create' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'formCreazione',
            ],
            'annuncio/store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'crea',
                'params' => fn() => [$_POST, currentUserId(), $_FILES],
            ],
            'annuncio/edit' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'formModifica',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'annuncio/update' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'aggiorna',
                'params' => fn() => [$_POST, currentUserId(), $_FILES],
            ],
            'annuncio/image-delete' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'eliminaImmagine',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'annuncio/delete' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => AnnuncioController::class,
                'action' => 'elimina',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],

            'auth/login' => [
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'loginFormOrSubmit',
                'params' => fn() => [$_POST],
            ],
            'auth/logout' => [
                'middleware' => [fn() => requireAuth()],
                'controller' => UtenteController::class,
                'action' => 'logout',
            ],
            'auth/register' => [
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'showRegister',
            ],
            'auth/register-user' => [
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'registerUserFormOrSubmit',
                'params' => fn() => [$_POST],
            ],
            'auth/register-business' => [
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'registerBusinessFormOrSubmit',
                'params' => fn() => [$_POST],
            ],
            'auth/verifica-email-attesa' => [
                'controller' => UtenteController::class,
                'action' => 'verificaEmailAttesa',
            ],
            'auth/verifica-email' => [
                'urlParams' => true,
                'controller' => UtenteController::class,
                'action' => 'verificaEmail',
                'params' => fn(array $params) => [$params[0] ?? ''],
            ],
            'auth/reinvia-verifica' => [
                'controller' => UtenteController::class,
                'action' => 'reinviaVerifica',
                'params' => fn() => [$_POST],
            ],
            'auth/recupero-password' => [
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'passwordRecoveryFormOrSubmit',
                'params' => fn() => [$_POST],
            ],
            'auth/reset-password' => [
                'urlParams' => true,
                'middleware' => [fn() => requireGuest()],
                'controller' => UtenteController::class,
                'action' => 'passwordResetFormOrSubmit',
                'params' => fn(array $params) => [$_POST, $params[0] ?? ''],
            ],

            'utente/profilo' => [
                'middleware' => [fn() => requireAuth()],
                'controller' => UtenteController::class,
                'action' => 'profiloCorrente',
                'params' => fn() => [currentUserId(), 'attivo'],
            ],
            'utente/profilo-venduti' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => UtenteController::class,
                'action' => 'profilo',
                'params' => fn() => [currentUserId(), 'venduto'],
            ],
            'utente/venditore' => [
                'urlParams' => true,
                'controller' => UtenteController::class,
                'action' => 'venditore',
                'params' => fn(array $params) => [$this->id($params)],
            ],
            'utente/propic-store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => UtenteController::class,
                'action' => 'aggiornaFotoProfilo',
                'params' => fn() => [$_FILES, currentUserId()],
            ],
            'utente/update' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => UtenteController::class,
                'action' => 'aggiornaProfiloUtente',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'utente/password' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => UtenteController::class,
                'action' => 'cambiaPassword',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'utente/indirizzo-store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => UtenteController::class,
                'action' => 'salvaIndirizzoSpedizione',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'utente/indirizzo-default' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => UtenteController::class,
                'action' => 'impostaIndirizzoPredefinito',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'utente/indirizzo-edit' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => UtenteController::class,
                'action' => 'showModificaIndirizzo',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'utente/indirizzo-update' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => UtenteController::class,
                'action' => 'aggiornaIndirizzo',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'utente/indirizzo-delete' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => UtenteController::class,
                'action' => 'eliminaIndirizzo',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],

            'carrello/list' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => CarrelloController::class,
                'action' => 'lista',
                'params' => fn() => [currentUserId()],
            ],
            'carrello/add' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => CarrelloController::class,
                'action' => 'aggiungi',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'carrello/remove' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => CarrelloController::class,
                'action' => 'rimuovi',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'carrello/clear' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => CarrelloController::class,
                'action' => 'svuota',
                'params' => fn() => [currentUserId()],
            ],

            'wishlist/list' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => WishlistController::class,
                'action' => 'lista',
                'params' => fn() => [currentUserId()],
            ],
            'wishlist/add' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => WishlistController::class,
                'action' => 'aggiungi',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'wishlist/remove' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => WishlistController::class,
                'action' => 'rimuovi',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'wishlist/toggle' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => WishlistController::class,
                'action' => 'toggle',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'wishlist/clear' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => WishlistController::class,
                'action' => 'svuota',
                'params' => fn() => [currentUserId()],
            ],

            'pagamento/checkout' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'checkout',
                'params' => fn(array $params) => [currentUserId(), $this->id($params)],
            ],
            'pagamento/checkout-carrello' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'checkoutCarrello',
                'params' => fn() => [currentUserId()],
            ],
            'pagamento/paypal-carrello' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'paypalPlaceholderCarrello',
                'params' => fn(array $params) => [currentUserId(), (int) ($params[0] ?? $_POST['id_indirizzo'] ?? 0)],
            ],
            'pagamento/conferma-carrello' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'confermaCarrello',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'pagamento/paypal' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'paypalPlaceholder',
                'params' => fn(array $params) => [
                    currentUserId(),
                    (int) ($params[0] ?? $_POST['id_annuncio'] ?? 0),
                    (int) ($_POST['id_indirizzo'] ?? 0),
                ],
            ],
            'pagamento/cancel' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'paypalCancel',
            ],
            'pagamento/conferma' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => denyBusiness()],
                'controller' => PagamentoController::class,
                'action' => 'conferma',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'pagamento/esito' => [
                'middleware' => [fn() => requireAuth()],
                'controller' => PagamentoController::class,
                'action' => 'esito',
            ],

            'business/dashboard' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => BusinessController::class,
                'action' => 'dashboard',
                'params' => fn() => [currentUserId()],
            ],
            'business/create' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => BusinessController::class,
                'action' => 'formCreazione',
            ],
            'business/store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => BusinessController::class,
                'action' => 'creaAccount',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'business/ordini' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => requireBusiness()],
                'controller' => BusinessController::class,
                'action' => 'ordini',
                'params' => fn() => [currentUserId()],
            ],
            'business/info-store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => requireBusiness($this->pdo)],
                'controller' => BusinessController::class,
                'action' => 'salvaInfo',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'business/indirizzo-store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin(), fn() => requireBusiness()],
                'controller' => BusinessController::class,
                'action' => 'salvaIndirizzo',
                'params' => fn() => [$_POST, currentUserId()],
            ],

            'feedback/list' => [
                'middleware' => [fn() => requireAuth()],
                'controller' => FeedbackController::class,
                'action' => 'lista',
                'params' => fn() => [currentUserId()],
            ],
            'feedback/create' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => FeedbackController::class,
                'action' => 'form',
                'params' => fn(array $params) => [(int) ($_GET['id_pagamento'] ?? $params[0] ?? 0), currentUserId()],
            ],
            'feedback/store' => [
                'middleware' => [fn() => requireAuth(), fn() => denyAdmin()],
                'controller' => FeedbackController::class,
                'action' => 'crea',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'feedback/venditore' => [
                'urlParams' => true,
                'controller' => FeedbackController::class,
                'action' => 'listaVenditore',
                'params' => fn(array $params) => [$this->id($params)],
            ],

            'segnalazione/create' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAuth()],
                'controller' => SegnalazioneController::class,
                'action' => 'form',
                'params' => fn(array $params) => [(int) ($params[0] ?? 0)],
            ],
            'segnalazione/store' => [
                'middleware' => [fn() => requireAuth()],
                'controller' => SegnalazioneController::class,
                'action' => 'crea',
                'params' => fn() => [$_POST, currentUserId()],
            ],
            'segnalazione/close' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdmin()],
                'controller' => SegnalazioneController::class,
                'action' => 'chiudi',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'segnalazione/delete' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdmin()],
                'controller' => SegnalazioneController::class,
                'action' => 'elimina',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],

            'admin/index' => [
                'middleware' => [fn() => requireAdmin()],
                'controller' => AdminController::class,
                'action' => 'dashboard',
                'params' => fn() => [currentUserId()],
            ],
            'admin/dashboard' => [
                'middleware' => [fn() => requireAdminLivello2()],
                'controller' => AdminController::class,
                'action' => 'dashboardModerazione',
                'params' => fn() => [$_GET],
            ],
            'admin/utenti' => [
                'middleware' => [fn() => requireAdmin()],
                'controller' => AdminController::class,
                'action' => 'utenti',
                'params' => fn() => [$_GET],
            ],
            'admin/banna-utente' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdmin()],
                'controller' => AdminController::class,
                'action' => 'bannaUtente',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'admin/sblocca-utente' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdmin()],
                'controller' => AdminController::class,
                'action' => 'sbloccaUtente',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'admin/banna-admin' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdminLivello2()],
                'controller' => AdminController::class,
                'action' => 'bannaAdmin',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'admin/sblocca-admin' => [
                'urlParams' => true,
                'middleware' => [fn() => requireAdminLivello2()],
                'controller' => AdminController::class,
                'action' => 'sbloccaAdmin',
                'params' => fn(array $params) => [$this->id($params), currentUserId()],
            ],
            'admin/segnalazioni' => [
                'middleware' => [fn() => requireAdmin()],
                'controller' => AdminController::class,
                'action' => 'segnalazioni',
                'params' => fn() => [$_GET],
            ],

            'legale/privacy' => [
                'controller' => LegalController::class,
                'action' => 'privacy',
            ],
            'legale/termini' => [
                'controller' => LegalController::class,
                'action' => 'termini',
            ],
            'legale/cookie' => [
                'controller' => LegalController::class,
                'action' => 'cookie',
            ],
        ];
    }

    private function requestSegments(): array
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (!empty($_SERVER['PATH_INFO'])) {
            $path = (string) $_SERVER['PATH_INFO'];
        }

        $path = rawurldecode(str_replace('\\', '/', $path));
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($scriptName !== '' && str_starts_with($path, $scriptName)) {
            $path = substr($path, strlen($scriptName));
        } elseif ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        $path = preg_replace('#^/?WEB_APP/public#i', '', $path) ?? $path;
        $path = preg_replace('#^/?index\.php#i', '', ltrim($path, '/')) ?? $path;

        return $this->segmentsFromString($path);
    }

    private function segmentsFromString(string $route): array
    {
        $route = str_replace(['_', ' '], '-', trim($route));
        $route = trim($route, '/');

        if ($route === '') {
            return ['home', 'index'];
        }

        return array_values(array_filter(
            array_map(static fn(string $part) => strtolower(trim($part)), explode('/', $route)),
            static fn(string $part) => $part !== ''
        ));
    }

    private function matchRoute(array $segments): array
    {
        $routes = $this->routes();

        for ($length = count($segments); $length >= 1; $length--) {
            $path = implode('/', array_slice($segments, 0, $length));
            if (!isset($routes[$path])) {
                continue;
            }

            $params = array_slice($segments, $length);
            if ($params === [] || !empty($routes[$path]['urlParams'])) {
                return [$params, $routes[$path]];
            }
        }

        return [[], null];
    }

    private function callController(string $className, string $action, array $params = []): bool
    {
        if (!class_exists($className) || !$this->canCall($className, $action)) {
            return false;
        }

        $controller = new $className($this->pdo);
        $controller->$action(...$params);
        return true;
    }

    private function canCall(string $className, string $action): bool
    {
        if (!method_exists($className, $action)) {
            return false;
        }

        $method = new ReflectionMethod($className, $action);
        return $method->isPublic() && !$method->isConstructor() && $method->getDeclaringClass()->getName() !== BaseController::class;
    }

    private function id(array $params = []): int
    {
        return (int) ($params[0] ?? 0);
    }
}
