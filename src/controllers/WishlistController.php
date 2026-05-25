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

class WishlistController
{
    private WishlistService $wishlistService;

    public function __construct(PDO $db)
    {
        $this->wishlistService = new WishlistService($db);
    }

    public function lista(int $idUtente): void
    {
        $wishlist = $this->wishlistService->getWishlistUtente($idUtente);

        require __DIR__ . '/../views/wishlist/lista.php';
    }

    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->wishlistService->aggiungiAnnuncio($idUtente, $idAnnuncio);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function rimuovi(int $idUtente, int $idAnnuncio): void
    {
        $this->wishlistService->rimuoviAnnuncio($idUtente, $idAnnuncio);

        header('Location: index.php?route=wishlist');
        exit;
    }

    public function toggle(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->wishlistService->toggleAnnuncio($idUtente, $idAnnuncio);

            $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=annunci';
            header('Location: ' . $redirect);
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function svuota(int $idUtente): void
    {
        $this->wishlistService->svuota($idUtente);

        header('Location: index.php?route=wishlist');
        exit;
    }
}
