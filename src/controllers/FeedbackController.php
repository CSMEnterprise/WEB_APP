<?php

namespace App\Controllers;

use App\Entity\EFeedback;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;

class FeedbackController extends BaseController
{
    public function __construct(PDO $db)
    {
        FDataBase::init($db);
    }

    public function form(int $idPagamento, int $idAutore): void
    {
        $pagamentoEntity = FPersistentManager::pagamentoById($idPagamento);
        $pagamento = $this->entityToArray($pagamentoEntity);

        if (!$pagamentoEntity || $pagamentoEntity->getIdAcquirente() !== $idAutore) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/400.php';
            return;
        }

        if (FPersistentManager::feedbackExists($idPagamento, $idAutore)) {
            header('Location: index.php?route=profilo');
            exit;
        }

        require __DIR__ . '/../views/feedback/form.php';
    }

    public function crea(array $data, int $idAutore): void
    {
        try {
            $this->createFeedback($data, $idAutore);

            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $idPagamento = (int) ($data['id_pagamento'] ?? 0);
            $pagamento = $this->entityToArray(FPersistentManager::pagamentoById($idPagamento));

            require __DIR__ . '/../views/feedback/form.php';
        }
    }

    public function lista(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $feedback = $this->entitiesToArrays(FPersistentManager::feedbackByUser($idUtente));

        require __DIR__ . '/../views/feedback/lista.php';
    }

    public function listaVenditore(int $idVenditore): void
    {
        $this->requirePositiveId($idVenditore, 'Venditore');

        $feedback = $this->entitiesToArrays(FPersistentManager::feedbackByVenditore($idVenditore));
        $media = FPersistentManager::mediaFeedbackVenditore($idVenditore);

        require __DIR__ . '/../views/feedback/lista_venditore.php';
    }

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
