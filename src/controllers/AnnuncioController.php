<?php

class AnnuncioController
{
    public function lista(): void
    {
        global $pdo;

        $stmt = $pdo->query("
            SELECT * 
            FROM Annuncio
            ORDER BY data_pubblicazione DESC
        ");

        $annunci = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/annunci/lista.php';
    }

    public function dettaglio(int $idAnnuncio): void
    {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT * 
            FROM Annuncio
            WHERE id_annuncio = ?
        ");

        $stmt->execute([$idAnnuncio]);

        $annuncio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$annuncio) {
            http_response_code(404);
            echo "Annuncio non trovato";
            return;
        }

        require __DIR__ . '/../views/annunci/dettaglio.php';
    }
}
