<?php

namespace App\Controllers;

use App\Entity\EFeedback;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;

/**
 * Gestisce creazione e consultazione dei feedback post-acquisto.
 */
class FeedbackController extends BaseController
{
    /**
     * Mostra il form solo all'acquirente del pagamento e solo se non ha gia recensito.
     */
    public function form(int $idPagamento, int $idAutore): void
    {
        $pagamentoEntity = FPersistentManager::pagamentoById($idPagamento);
        $pagamento = $this->entityToArray($pagamentoEntity);

        if (!$pagamentoEntity || $pagamentoEntity->getIdAcquirente() !== $idAutore) {
            $this->renderError('Non puoi lasciare un feedback per questo pagamento.', 403);
            return;
        }

        if (FPersistentManager::feedbackExists($idPagamento, $idAutore)) {
            header('Location: /utente/profilo');
            exit;
        }

        $this->view('feedback/form.tpl', compact('idPagamento', 'pagamento'), 'Lascia feedback');
    }

    /**
     * Salva il feedback e, in caso di errore, ripresenta lo stesso form.
     */
    public function crea(array $data, int $idAutore): void
    {
        try {
            $this->createFeedback($data, $idAutore);

            header('Location: /utente/profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $idPagamento = (int) ($data['id_pagamento'] ?? 0);
            $pagamento = $this->entityToArray(FPersistentManager::pagamentoById($idPagamento));

            $this->view('feedback/form.tpl', compact('errore', 'idPagamento', 'pagamento'), 'Lascia feedback');
        }
    }

    /**
     * Mostra i feedback scritti o ricevuti dall'utente corrente.
     */
    public function lista(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $feedback = $this->entitiesToArrays(FPersistentManager::feedbackByUser($idUtente));

        $this->view('feedback/lista.tpl', compact('feedback'), 'I miei feedback');
    }

    /**
     * Mostra feedback pubblici e media voto di un venditore.
     */
    public function listaVenditore(int $idVenditore): void
    {
        $this->requirePositiveId($idVenditore, 'Venditore');

        $feedback = $this->entitiesToArrays(FPersistentManager::feedbackByVenditore($idVenditore));
        $media = FPersistentManager::mediaFeedbackVenditore($idVenditore);

        $this->view('feedback/lista_venditore.tpl', compact('feedback', 'media'), 'Feedback venditore');
    }

    /**
     * Normalizza dati del form e applica i vincoli prima del salvataggio.
     */
    private function createFeedback(array $data, int $idAutore): int
    {
        $feedback = EFeedback::fromArray(array_merge($data, [
            'id_autore' => $idAutore,
            'valutazione' => (int) ($data['valutazione'] ?? $data['voto'] ?? 0),
        ]));

        $this->requirePositiveId($feedback->getIdAutore(), 'Autore');

        if ($feedback->getIdPagamento() <= 0) {
            throw new ServiceException('Pagamento obbligatorio.');
        }

        if ($feedback->getValutazione() < 1 || $feedback->getValutazione() > 5) {
            throw new ServiceException('La valutazione deve essere compresa tra 1 e 5.');
        }

        $commento = $this->clean($feedback->getCommento());
        $feedback->setCommento($commento !== '' ? $commento : null);

        return FPersistentManager::createFeedback($feedback);
    }
}
