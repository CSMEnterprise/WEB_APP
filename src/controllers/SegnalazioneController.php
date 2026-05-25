<?php

namespace App\Controllers;

use App\Entity\EAccountBusiness;
use App\Entity\EAnnuncio;
use App\Entity\EIndirizzo;
use App\Entity\EPagamento;
use App\Entity\EUtenteRegistrato;
use App\Foundation\SmartyView;
use App\Services\AdminService;
use App\Services\AnnuncioService;
use App\Services\AuthService;
use App\Services\BusinessService;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\FeedbackService;
use App\Services\MailService;
use App\Services\PaymentService;
use App\Services\SegnalazioneService;
use App\Services\ServiceException;
use App\Services\UserService;
use App\Services\WishlistService;
use Exception;
use PDO;

class SegnalazioneController
{
    private SegnalazioneService $segnalazioneService;
    private AdminService $adminService;

    public function __construct(PDO $db)
    {
        $this->segnalazioneService = new SegnalazioneService($db);
        $this->adminService = new AdminService($db);
    }

    public function form(): void
    {
        require __DIR__ . '/../views/segnalazioni/form.php';
    }

    public function crea(array $data, int $idSegnalante): void
    {
        try {
            $this->segnalazioneService->crea($data, $idSegnalante);
            header('Location: index.php?route=annunci');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/segnalazioni/form.php';
        }
    }

    public function lista(): void
    {
        $segnalazioni = $this->segnalazioneService->getAll();
        require __DIR__ . '/../views/segnalazioni/lista.php';
    }

    public function chiudi(int $idSegnalazione, int $idAdmin): void
    {
        $this->segnalazioneService->chiudi($idSegnalazione, $idAdmin);
        $this->adminService->registraAzione($idAdmin, 'Segnalazione chiusa #' . $idSegnalazione);
        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }

    public function elimina(int $idSegnalazione, int $idAdmin): void
    {
        $this->adminService->registraAzione($idAdmin, 'Segnalazione eliminata #' . $idSegnalazione);
        $this->segnalazioneService->elimina($idSegnalazione);
        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }
}
