<?php

namespace App\Controllers;

use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use Exception;
use PDO;

class AdminController extends BaseController
{
    public function __construct(PDO $db)
    {
        FDataBase::init($db);
    }

    public function dashboard(int $idAdmin): void
    {
        $this->requirePositiveId($idAdmin, 'Admin');

        $stats = FPersistentManager::dashboardStats();
        $azioniModera = $this->entitiesToArrays(FPersistentManager::moderaByAdmin($idAdmin));

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function dashboardModerazione(array $filters): void
    {
        $azioniModerazione = $this->entitiesToArrays(FPersistentManager::azioniModerazione($filters));
        $filters = [
            'admin' => trim((string) ($filters['admin'] ?? '')),
        ];

        require __DIR__ . '/../views/admin/dashboard_moderazione.php';
    }

    public function utenti(array $filters = []): void
    {
        $searchUtente = trim((string) ($filters['q_utente'] ?? ''));
        $utenti = $this->entitiesToArrays(FPersistentManager::utentiForAdmin($searchUtente));
        $admins = ((int) ($_SESSION['livello_sicurezza'] ?? 1) === 2)
            ? $this->entitiesToArrays(FPersistentManager::admins())
            : [];
        $filters = ['q_utente' => $searchUtente];

        require __DIR__ . '/../views/admin/utenti.php';
    }

    public function bannaUtente(int $idUtente, int $idAdmin): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAdmin, 'Admin');

        FPersistentManager::setUtenteBanState($idUtente, true);
        $this->registerAdminAction($idAdmin, 'Utente bannato', $idUtente);

        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function sbloccaUtente(int $idUtente, int $idAdmin): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAdmin, 'Admin');

        FPersistentManager::setUtenteBanState($idUtente, false);
        $this->registerAdminAction($idAdmin, 'Utente sbloccato', $idUtente);

        header('Location: index.php?route=admin-utenti');
        exit;
    }

    public function bannaAdmin(int $idAdminDaBannare, int $idAdminCorrente): void
    {
        try {
            $target = $this->findAdminForModeration($idAdminDaBannare);
            $current = $this->findAdminForModeration($idAdminCorrente);

            $this->ensureCanModerateAdmin($target, $current);

            FPersistentManager::setAdminBanState($idAdminDaBannare, true);
            $this->registerAdminAction($idAdminCorrente, 'Admin bannato #' . $idAdminDaBannare);

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
            $target = $this->findAdminForModeration($idAdminDaSbloccare);
            $current = $this->findAdminForModeration($idAdminCorrente);

            $this->ensureCanModerateAdmin($target, $current);

            FPersistentManager::setAdminBanState($idAdminDaSbloccare, false);
            $this->registerAdminAction($idAdminCorrente, 'Admin sbloccato #' . $idAdminDaSbloccare);

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
        $segnalazioni = $this->entitiesToArrays(FPersistentManager::segnalazioni($filters));
        $filters = [
            'oggetto' => trim((string) ($filters['oggetto'] ?? '')),
            'tipologia' => trim((string) ($filters['tipologia'] ?? '')),
        ];

        require __DIR__ . '/../views/admin/segnalazioni.php';
    }

    public function eliminaAnnuncio(int $idAnnuncio, int $idAdmin): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idAdmin, 'Admin');

        $this->registerAdminAction($idAdmin, 'Annuncio eliminato #' . $idAnnuncio);
        FPersistentManager::deleteAnnuncioByAdmin($idAnnuncio);

        header('Location: index.php?route=annunci');
        exit;
    }
}
