<?php

require_once __DIR__ . '/../services/AdminService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/SegnalazioneService.php';
require_once __DIR__ . '/../services/AnnuncioService.php';

class AdminController
{
    private AdminService $adminService;
    private UserService $userService;
    private SegnalazioneService $segnalazioneService;
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        $this->adminService = new AdminService($db);
        $this->userService = new UserService($db);
        $this->segnalazioneService = new SegnalazioneService($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function dashboard(int $idAdmin): void
    {
        $stats = $this->adminService->getDashboardStats();
        $azioniModera = $this->adminService->getAzioniByAdmin($idAdmin);
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function dashboardModerazione(array $filters): void
    {
        $azioniModerazione = $this->adminService->getAzioniModerazione($filters);
        $filters = [
            'admin' => trim((string) ($filters['admin'] ?? '')),
        ];

        require __DIR__ . '/../views/admin/dashboard_moderazione.php';
    }

    public function utenti(array $filters = []): void
    {
        $searchUtente = trim((string) ($filters['q_utente'] ?? ''));
        $utenti = $this->userService->getAll($searchUtente);
        $admins = ((int) ($_SESSION['livello_sicurezza'] ?? 1) === 2)
            ? $this->adminService->getAllAdmins()
            : [];
        $filters = ['q_utente' => $searchUtente];

        require __DIR__ . '/../views/admin/utenti.php';
    }

    public function bannaUtente(int $idUtente, int $idAdmin): void
    {
        $this->userService->banna($idUtente);
        $this->adminService->registraAzione($idAdmin, 'Utente bannato', $idUtente);
        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function sbloccaUtente(int $idUtente, int $idAdmin): void
    {
        $this->userService->sblocca($idUtente);
        $this->adminService->registraAzione($idAdmin, 'Utente sbloccato', $idUtente);
        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function bannaAdmin(int $idAdminDaBannare, int $idAdminCorrente): void
    {
        try {
            $this->adminService->bannaAdminLivello1($idAdminDaBannare, $idAdminCorrente);
            $this->adminService->registraAzione($idAdminCorrente, 'Admin bannato #' . $idAdminDaBannare);
            header('Location: index.php?route=admin-utenti');
            exit;
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function sbloccaAdmin(int $idAdminDaSbloccare, int $idAdminCorrente): void
    {
        try {
            $this->adminService->sbloccaAdminLivello1($idAdminDaSbloccare, $idAdminCorrente);
            $this->adminService->registraAzione($idAdminCorrente, 'Admin sbloccato #' . $idAdminDaSbloccare);
            header('Location: index.php?route=admin-utenti');
            exit;
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function segnalazioni(array $filters = []): void
    {
        $segnalazioni = $this->segnalazioneService->getFiltrate($filters);
        $filters = [
            'oggetto' => trim((string) ($filters['oggetto'] ?? '')),
            'tipologia' => trim((string) ($filters['tipologia'] ?? '')),
        ];

        require __DIR__ . '/../views/admin/segnalazioni.php';
    }

    public function eliminaAnnuncio(int $idAnnuncio, int $idAdmin): void
    {
        $this->adminService->registraAzione($idAdmin, 'Annuncio eliminato #' . $idAnnuncio);
        $this->annuncioService->eliminaDaAdmin($idAnnuncio);
        header('Location: index.php?route=annunci');
        exit;
    }
}
