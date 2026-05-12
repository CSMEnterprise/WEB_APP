<?php

require_once __DIR__ . '/../services/BusinessService.php';
require_once __DIR__ . '/../services/AnnuncioService.php';

class BusinessController
{
    private BusinessService $businessService;
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        $this->businessService = new BusinessService($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function dashboard(int $idUtente): void
    {
        $business = $this->businessService->findByUserId($idUtente);
        $annunci = $this->annuncioService->getByUserId($idUtente);

        require __DIR__ . '/../views/business/profilo.php';
    }

    public function formCreazione(): void
    {
        require __DIR__ . '/../views/business/form.php';
    }

    public function creaAccount(array $data, int $idUtente): void
    {
        try {
            $this->businessService->creaAccount($data, $idUtente);
            header('Location: index.php?route=business');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/business/form.php';
        }
    }

    public function ordini(int $idUtente): void
    {
        $ordini = $this->businessService->getOrdiniRicevuti($idUtente);
        require __DIR__ . '/../views/business/ordini.php';
    }
}
