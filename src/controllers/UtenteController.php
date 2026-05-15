<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AnnuncioService.php';

class UtenteController
{
    private AuthService $authService;
    private UserService $userService;
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        $this->authService = new AuthService($db);
        $this->userService = new UserService($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../views/utenti/login.php';
    }

    public function login(array $data): void
    {
        try {
            $utente = $this->authService->login($data['email'] ?? '', $data['password'] ?? '');

            $_SESSION['user_id'] = (int) $utente['id_utente'];
            $_SESSION['username'] = $utente['username'];

            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/login.php';
        }
    }

    public function showRegister(): void
    {
        require __DIR__ . '/../views/utenti/registrazione.php';
    }

    public function register(array $data): void
    {
        try {
            $this->authService->register($data);
            header('Location: index.php?route=login');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/registrazione.php';
        }
    }

    public function profilo(int $idUtente): void
    {
        $utente = $this->userService->findById($idUtente);
        $annunciUtente = $this->annuncioService->getByUserId($idUtente);

        require __DIR__ . '/../views/utenti/profilo.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php?route=home');
        exit;
    }

    public function salvaIndirizzoSpedizione(array $data, int $idUtente): void
    {
        try {
            $this->userService->updateIndirizzoSpedizione($idUtente, $data);

            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $utente = $this->userService->findById($idUtente);
            $annunciUtente = $this->annuncioService->getByUserId($idUtente);

            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }
}
