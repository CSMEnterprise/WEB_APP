<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AnnuncioService.php';
require_once __DIR__ . '/../services/BusinessService.php';
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../services/FeedbackService.php';

class UtenteController
{
    private AuthService $authService;
    private UserService $userService;
    private AnnuncioService $annuncioService;
    private BusinessService $businessService;
    private PaymentService $paymentService;
    private FeedbackService $feedbackService;
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->authService = new AuthService($db);
        $this->userService = new UserService($db);
        $this->annuncioService = new AnnuncioService($db);
        $this->businessService = new BusinessService($db);
        $this->paymentService = new PaymentService($db);
        $this->feedbackService = new FeedbackService($db);
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../views/utenti/login.php';
    }

    public function login(array $data): void
    {
        try {
            $utente = $this->authService->login($data['email'] ?? '', $data['password'] ?? '');

            if (!empty($utente['_is_admin'])) {
                $_SESSION['user_id']  = (int) $utente['id_admin'];
                $_SESSION['username'] = 'Admin';
                $_SESSION['is_admin'] = true;
                $_SESSION['propic']   = null;
                $_SESSION['livello_sicurezza'] = (int) ($utente['livello_sicurezza'] ?? 1);
                header('Location: index.php?route=admin');
            } else {
                $_SESSION['user_id']  = (int) $utente['id_utente'];
                $_SESSION['username'] = $utente['username'];
                $_SESSION['propic']   = $utente['propic'] ?? null;
                header('Location: index.php?route=profilo');
            }
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

    public function register(array $data): void
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


    public function showRegisterBusiness(): void
    {
        require __DIR__ . '/../views/utenti/registrazione_business.php';
    }

    public function registerBusiness(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $data['_business_registration'] = true;
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

    public function profilo(int $idUtente, string $filtroAnnunci = 'attivo'): void
    {
        $utente = $this->userService->findById($idUtente);
        $filtroAnnunci = $filtroAnnunci === 'venduto' ? 'venduto' : 'attivo';
        $annunciUtente = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
        $titoloAnnunciProfilo = $filtroAnnunci === 'venduto' ? 'Annunci venduti' : 'Annunci attivi';
        $cronologiaPagamenti = $this->paymentService->getCronologiaByUserId($idUtente);

        require __DIR__ . '/../views/utenti/profilo.php';
    }

    public function venditore(int $idVenditore): void
    {
        if ($idVenditore <= 0) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $venditore = $this->userService->findById($idVenditore);

        if (!$venditore || !empty($venditore['stato_ban'])) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $annunciVenditore = $this->annuncioService->getByUserIdAndStato($idVenditore, 'attivo');
        $feedbackVenditore = $this->feedbackService->getByVenditoreId($idVenditore);
        $mediaVenditore = $this->feedbackService->getMediaVoto($idVenditore);

        require __DIR__ . '/../views/utenti/venditore.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php?route=home');
        exit;
    }

    public function aggiornaFotoProfilo(array $files, int $idUtente): void
    {
        try {
            $url = $this->userService->updatePropic($idUtente, $files['propic'] ?? []);
            $_SESSION['propic'] = $url;
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore          = $e->getMessage();
            $utente          = $this->userService->findById($idUtente);
            $filtroAnnunci   = 'attivo';
            $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti  = $this->paymentService->getCronologiaByUserId($idUtente);
            require __DIR__ . '/../views/utenti/profilo.php';
        }
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
            $filtroAnnunci = 'attivo';
            $annunciUtente = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';

            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }
}
