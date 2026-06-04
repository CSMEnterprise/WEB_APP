<?php

namespace App\Controllers;

use App\Entity\{
    EAnnuncio,
    EElementoCarrello
};
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;

/**
 * Gestisce carrello acquisti: lista, aggiunta, rimozione e svuotamento.
 */
class CarrelloController extends BaseController
{
    /**
     * Mostra il carrello pulendo prima eventuali articoli non piu acquistabili.
     */
    public function lista(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            $annunciRimossi = FPersistentManager::unavailableCartItems($idCarrello);

            if (!empty($annunciRimossi)) {
                FPersistentManager::removeUnavailableCartItems($idCarrello);
            }

            $carrello = FPersistentManager::elementiCarrelloAcquistabili($idCarrello);
            $purchasableItems = [];
            $totale = 0.0;

            // La view riceve sia il carrello completo sia il sottoinsieme davvero acquistabile.
            foreach ($carrello as $item) {
                $isOwner = (int)($item['id_utente'] ?? 0) === $idUtente;

                if (!$isOwner && ($item['stato'] ?? '') === 'attivo') {
                    $purchasableItems[] = $item;
                    $totale += (float)($item['prezzo'] ?? 0);
                }
            }

            $this->view('carrello/lista.tpl', compact('annunciRimossi', 'carrello', 'purchasableItems', 'totale'), 'Carrello');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Aggiunge un annuncio al carrello e torna alla pagina di provenienza.
     */
    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->aggiungiAnnuncioAlCarrello($idUtente, $idAnnuncio);

            $back = $_SERVER['HTTP_REFERER'] ?? '/annuncio/list';
            header('Location: ' . $back);
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Rimuove un singolo annuncio dal carrello dell'utente.
     */
    public function rimuovi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->requirePositiveId($idAnnuncio, 'Annuncio');
            $this->denyBusinessBuyer($idUtente);

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            FPersistentManager::removeElementoCarrello($idCarrello, $idAnnuncio);

            header('Location: /carrello/list');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Svuota completamente il carrello.
     */
    public function svuota(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            FPersistentManager::clearCart($idCarrello);

            header('Location: /carrello/list');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Contiene le regole di business per aggiungere solo annunci comprabili.
     */
    private function aggiungiAnnuncioAlCarrello(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->denyBusinessBuyer($idUtente);

        $annuncio = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncio instanceof EAnnuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException("Questo annuncio non e' acquistabile.");
        }

        if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere al carrello un tuo annuncio.');
        }

        $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);

        FPersistentManager::addElementoCarrello(new EElementoCarrello($idCarrello, $idAnnuncio));
        // Un prodotto nel carrello non deve comparire anche tra i preferiti dello stesso utente.
        FPersistentManager::removePreferito($idUtente, $idAnnuncio);
    }
}
