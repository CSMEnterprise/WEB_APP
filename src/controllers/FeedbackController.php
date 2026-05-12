<?php

require_once __DIR__ . '/../services/FeedbackService.php';

class FeedbackController
{
    private FeedbackService $feedbackService;

    public function __construct(PDO $db)
    {
        $this->feedbackService = new FeedbackService($db);
    }

    public function crea(array $data, int $idAutore): void
    {
        try {
            $this->feedbackService->crea($data, $idAutore);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function lista(int $idUtente): void
    {
        $feedback = $this->feedbackService->getByUserId($idUtente);
        require __DIR__ . '/../views/feedback/lista.php';
    }
}
