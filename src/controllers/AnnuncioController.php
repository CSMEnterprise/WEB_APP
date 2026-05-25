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

class AnnuncioController
{
    private AnnuncioService  $annuncioService;
    private CategoryService  $categoryService;
    private FeedbackService  $feedbackService;
    private UserService      $userService;
    private WishlistService  $wishlistService;
    private CartService      $cartService;

    public function __construct(PDO $db)
    {
        $this->annuncioService = new AnnuncioService($db);
        $this->categoryService = new CategoryService($db);
        $this->feedbackService = new FeedbackService($db);
        $this->userService     = new UserService($db);
        $this->wishlistService = new WishlistService($db);
        $this->cartService     = new CartService($db);
    }

    public function lista(): void
    {
        $q = trim($_GET['q'] ?? '');
        $idCategoria = (int) ($_GET['id_categoria'] ?? 0);
        $categorie = $this->categoryService->getAll();

        if ($q !== '' || $idCategoria > 0) {
            $annunci = $this->annuncioService->searchAnnunci($q, $idCategoria);
            $utenti  = $this->userService->search($q);
        } else {
            $annunci = $this->annuncioService->getAnnunciAttivi();
            $utenti  = [];
        }

        $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);

        $wishlistIds = $isRegularUser
            ? $this->wishlistService->getWishlistIds((int) $_SESSION['user_id'])
            : [];

        require __DIR__ . '/../views/annunci/lista.php';
    }

    public function dettaglio(int $idAnnuncio): void
    {
        $annuncio = $this->annuncioService->findById($idAnnuncio);

        if (!$annuncio) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $annuncioEntity     = EAnnuncio::fromArray($annuncio);
        $idVenditore        = (int) ($annuncioEntity->getIdUtente() ?? 0);
        $feedbackVenditore  = $idVenditore > 0 ? $this->feedbackService->getByVenditoreId($idVenditore) : [];
        $mediaVenditore     = $idVenditore > 0 ? $this->feedbackService->getMediaVoto($idVenditore) : 0.0;

        $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);
        $wishlistIds = $isRegularUser ? $this->wishlistService->getWishlistIds((int) $_SESSION['user_id']) : [];
        $carrelloIds = $isRegularUser ? $this->cartService->getCarrelloIds((int) $_SESSION['user_id']) : [];

        require __DIR__ . '/../views/annunci/dettaglio.php';
    }

    public function formCreazione(): void
    {
        $categorie = $this->categoryService->getAll();
        require __DIR__ . '/../views/annunci/form.php';
    }

    public function formModifica(int $idAnnuncio, int $idUtente): void
    {
        try {
            $annuncio = $this->annuncioService->findById($idAnnuncio);
            $annuncioEntity = $annuncio ? EAnnuncio::fromArray($annuncio) : null;

            if (!$annuncioEntity || (int)($annuncioEntity->getIdUtente() ?? 0) !== $idUtente || !$annuncioEntity->isAttivo()) {
                throw new ServiceException('Non puoi modificare questo annuncio.');
            }

            $categorie = $this->categoryService->getAll();
            $isEdit = true;
            require __DIR__ . '/../views/annunci/form.php';
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function crea(array $data, int $idUtente, array $files = []): void
    {
        try {
            $idAnnuncio = $this->annuncioService->crea($data, $idUtente, $files);
            header('Location: index.php?route=annuncio&id=' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $categorie = $this->categoryService->getAll();
            require __DIR__ . '/../views/annunci/form.php';
        }
    }

    public function aggiorna(array $data, int $idUtente, array $files = []): void
    {
        $idAnnuncio = (int)($data['id_annuncio'] ?? 0);

        try {
            $this->annuncioService->aggiorna($idAnnuncio, $idUtente, $data, $files);
            header('Location: index.php?route=annuncio&id=' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $categorie = $this->categoryService->getAll();
            $annuncio = $idAnnuncio > 0 ? ($this->annuncioService->findById($idAnnuncio) ?: $data) : $data;
            $isEdit = true;
            require __DIR__ . '/../views/annunci/form.php';
        }
    }

    public function eliminaImmagine(array $data, int $idUtente): void
    {
        try {
            $idAnnuncio = $this->annuncioService->eliminaImmagine((int)($data['id_immagine'] ?? 0), $idUtente);
            header('Location: index.php?route=annuncio-edit&id=' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function elimina(int $idAnnuncio, int $idUtente): void
    {
        try {
            $this->annuncioService->elimina($idAnnuncio, $idUtente);
            header('Location: index.php?route=annunci');
            exit;
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }
}
