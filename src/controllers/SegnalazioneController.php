<?php

require_once __DIR__ . '/../services/SegnalazioneService.php';

class SegnalazioneController
{
    private SegnalazioneService $segnalazioneService;

    public function __construct(PDO $db)
    {
        $this->segnalazioneService = new SegnalazioneService($db);
    }

    public function form(): void
    {
        require __DIR__ . '/../views/segnalazioni/form.php';
    }

    public function crea(array $data, int $idSegnalante): void
    {
        try {
            $this->segnalazioneService->crea($data, $idSegnalante);
            header('Location: index.php?route=annunci');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/segnalazioni/form.php';
        }
    }

    public function lista(): void
    {
        $segnalazioni = $this->segnalazioneService->getAll();
        require __DIR__ . '/../views/segnalazioni/lista.php';
    }

    public function chiudi(int $idSegnalazione): void
    {
        $this->segnalazioneService->chiudi($idSegnalazione);
        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }

    public function elimina(int $idSegnalazione): void
    {
        $this->segnalazioneService->elimina($idSegnalazione);
        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }
}
