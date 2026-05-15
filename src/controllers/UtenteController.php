<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/BusinessService.php';

class UtenteController
{
    private PDO $db;
    private AuthService $authService;
    private UserService $userService;
    private BusinessService $businessService;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->authService = new AuthService($db);
        $this->userService = new UserService($db);
        $this->businessService = new BusinessService($db);
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

    public function showRegisterUser(): void
    {
        require __DIR__ . '/../views/utenti/registrazione_utente.php';
    }

    public function showRegisterBusiness(): void
    {
        require __DIR__ . '/../views/utenti/registrazione_business.php';
    }

    public function register(array $data): void
    {
        $this->registerUser($data);
    }

    public function registerUser(array $data): void
    {
        try {
            $this->authService->register($data);
            header('Location: index.php?route=login');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/registrazione_utente.php';
        }
    }

    public function registerBusiness(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $nomeAzienda = trim($data['nome_azienda'] ?? '');
            $slugAzienda = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $nomeAzienda));
            $slugAzienda = trim($slugAzienda, '_');

            $data['_business_registration'] = true;
            $data['email'] = $data['email_aziendale'] ?? '';
            $data['username'] = $slugAzienda !== ''
                ? 'business_' . $slugAzienda . '_' . substr(md5((string) microtime(true)), 0, 6)
                : '';

            $idUtente = $this->authService->register($data);
            $this->businessService->creaAccount($data, $idUtente);

            $this->db->commit();

            header('Location: index.php?route=login');
            exit;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/registrazione_business.php';
        }
    }

    public function profilo(int $idUtente): void
    {
        $utente = $this->userService->findById($idUtente);
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

            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }
}
