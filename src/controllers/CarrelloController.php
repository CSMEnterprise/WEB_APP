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
