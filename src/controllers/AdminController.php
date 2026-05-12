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

    public function dashboard(): void
    {
        $stats = $this->adminService->getDashboardStats();
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function utenti(): void
    {
        $utenti = $this->userService->getAll();
        require __DIR__ . '/../views/admin/utenti.php';
    }

    public function bannaUtente(int $idUtente): void
    {
        $this->userService->banna($idUtente);
        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function sbloccaUtente(int $idUtente): void
    {
        $this->userService->sblocca($idUtente);
        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function segnalazioni(): void
    {
        $segnalazioni = $this->segnalazioneService->getAll();
        require __DIR__ . '/../views/admin/segnalazioni.php';
    }

    public function eliminaAnnuncio(int $idAnnuncio): void
    {
        $this->annuncioService->eliminaDaAdmin($idAnnuncio);
        header('Location: index.php?route=annunci');
        exit;
    }
}
