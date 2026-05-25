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

class BusinessController
{
    private BusinessService $businessService;
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        $this->businessService = new BusinessService($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function dashboard(int $idUtente): void
    {
        $business = $this->businessService->findByUserId($idUtente);
        $annunci  = $this->annuncioService->getByUserId($idUtente);

        require __DIR__ . '/../views/business/profilo.php';
    }

    public function formCreazione(): void
    {
        require __DIR__ . '/../views/business/form.php';
    }

    public function creaAccount(array $data, int $idUtente): void
    {
        try {
            $this->businessService->creaAccount($data, $idUtente);
            header('Location: index.php?route=business');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/business/form.php';
        }
    }

    public function salvaIndirizzo(array $data, int $idUtente): void
    {
        $business = $this->businessService->findByUserId($idUtente);
        $businessEntity = $business ? EAccountBusiness::fromArray($business) : null;

        if (!$businessEntity) {
            header('Location: index.php?route=business');
            exit;
        }

        try {
            $this->businessService->aggiornaIndirizzo(
                (int) ($businessEntity->getIdAccBusiness() ?? 0),
                $data
            );
            header('Location: index.php?route=business');
            exit;
        } catch (Exception $e) {
            $errore  = $e->getMessage();
            $annunci = $this->annuncioService->getByUserId($idUtente);
            require __DIR__ . '/../views/business/profilo.php';
        }
    }

    public function ordini(int $idUtente): void
    {
        $ordini = $this->businessService->getOrdiniRicevuti($idUtente);
        require __DIR__ . '/../views/business/ordini.php';
    }
}
