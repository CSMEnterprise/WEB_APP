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

class CarrelloController extends BaseController
{
    private CartService $cartService;

    public function __construct(PDO $db)
    {
        $this->cartService = new CartService($db);
    }

    public function lista(int $idUtente): void
    {
        $carrello = $this->entitiesToArrays($this->cartService->getCarrelloUtenteEntity($idUtente));
        $annunciRimossi = $this->cartService->getUltimiAnnunciRimossi();
        $totale = 0.0;

        foreach ($carrello as $item) {
            $isOwner = (int)($item['id_utente'] ?? 0) === $idUtente;

            if (!$isOwner && ($item['stato'] ?? '') === 'attivo') {
                $totale += (float)($item['prezzo'] ?? 0);
            }
        }

        require __DIR__ . '/../views/carrello/lista.php';
    }

    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->cartService->aggiungiAnnuncio($idUtente, $idAnnuncio);

            $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=annunci';
            header('Location: ' . $back);
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function rimuovi(int $idUtente, int $idAnnuncio): void
    {
        $this->cartService->rimuoviAnnuncio($idUtente, $idAnnuncio);

        header('Location: index.php?route=carrello');
        exit;
    }

    public function svuota(int $idUtente): void
    {
        $this->cartService->svuota($idUtente);

        header('Location: index.php?route=carrello');
        exit;
    }
}
