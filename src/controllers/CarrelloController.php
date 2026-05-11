<?php

require_once __DIR__ . '/../services/CartService.php';

class CarrelloController
{
    private CartService $cartService;

    public function __construct(PDO $db)
    {
        $this->cartService = new CartService($db);
    }

    public function lista(int $idUtente): void
    {
        $carrello = $this->cartService->getCarrelloUtente($idUtente);
        $totale = $this->cartService->getTotale($idUtente);

        require __DIR__ . '/../views/carrello/lista.php';
    }

    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        $this->cartService->aggiungiAnnuncio($idUtente, $idAnnuncio);

        header('Location: index.php?route=carrello');
        exit;
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
