<?php

require_once __DIR__ . '/../Entity/EPagamento.php';
require_once __DIR__ . '/../services/FeedbackService.php';
require_once __DIR__ . '/../services/PaymentService.php';

class FeedbackController
{
    private FeedbackService $feedbackService;
    private PaymentService  $paymentService;

    public function __construct(PDO $db)
    {
        $this->feedbackService = new FeedbackService($db);
        $this->paymentService  = new PaymentService($db);
    }

    public function form(int $idPagamento, int $idAutore): void
    {
        $pagamento = $this->paymentService->findById($idPagamento);
        $pagamentoEntity = $pagamento ? EPagamento::fromArray($pagamento) : null;

        if (!$pagamentoEntity || $pagamentoEntity->getIdAcquirente() !== $idAutore) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/400.php';
            return;
        }

        if ($this->feedbackService->hasFeedback($idPagamento, $idAutore)) {
            header('Location: index.php?route=profilo');
            exit;
        }

        require __DIR__ . '/../views/feedback/form.php';
    }

    public function crea(array $data, int $idAutore): void
    {
        try {
            $this->feedbackService->crea($data, $idAutore);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore   = $e->getMessage();
            $idPagamento = (int) ($data['id_pagamento'] ?? 0);
            $pagamento   = $this->paymentService->findById($idPagamento);
            require __DIR__ . '/../views/feedback/form.php';
        }
    }

    public function lista(int $idUtente): void
    {
        $feedback = $this->feedbackService->getByUserId($idUtente);
        require __DIR__ . '/../views/feedback/lista.php';
    }

    public function listaVenditore(int $idVenditore): void
    {
        $feedback = $this->feedbackService->getByVenditoreId($idVenditore);
        $media    = $this->feedbackService->getMediaVoto($idVenditore);
        require __DIR__ . '/../views/feedback/lista_venditore.php';
    }
}
