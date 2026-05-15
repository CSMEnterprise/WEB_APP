<?php

require_once __DIR__ . '/../services/AnnuncioService.php';
require_once __DIR__ . '/../services/CategoryService.php';

class AnnuncioController
{
    private AnnuncioService $annuncioService;
    private CategoryService $categoryService;

    public function __construct(PDO $db)
    {
        $this->annuncioService = new AnnuncioService($db);
        $this->categoryService = new CategoryService($db);
    }

    public function lista(): void
    {
        $q = $_GET['q'] ?? '';

        if (trim($q) !== '') {
            $annunci = $this->annuncioService->searchAnnunci($q);
        } else {
            $annunci = $this->annuncioService->getAnnunciAttivi();
        }

        require __DIR__ . '/../views/annunci/lista.php';
    }

    public function dettaglio(int $idAnnuncio): void
    {
        $annuncio = $this->annuncioService->findById($idAnnuncio);

        if (!$annuncio) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        require __DIR__ . '/../views/annunci/dettaglio.php';
    }

    public function formCreazione(): void
    {
        $categorie = $this->categoryService->getAll();
        require __DIR__ . '/../views/annunci/form.php';
    }

    public function crea(array $data, int $idUtente, array $files = []): void
    {
        try {
            $idAnnuncio = $this->annuncioService->crea($data, $idUtente, $files);
            header('Location: index.php?route=annuncio&id=' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $categorie = $this->categoryService->getAll();
            require __DIR__ . '/../views/annunci/form.php';
        }
    }

    public function elimina(int $idAnnuncio, int $idUtente): void
    {
        try {
            $this->annuncioService->elimina($idAnnuncio, $idUtente);
            header('Location: index.php?route=annunci');
            exit;
        } catch (Exception $e) {
            http_response_code(403);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }
}
