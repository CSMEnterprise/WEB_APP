<?php

require_once __DIR__ . '/../services/WishlistService.php';

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

    public function svuota(int $idUtente): void
    {
        $this->wishlistService->svuota($idUtente);

        header('Location: index.php?route=wishlist');
        exit;
    }
}
