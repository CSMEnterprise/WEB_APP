<?php

class CarrelloController
{
    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Carrello 
                (id_utente, id_annuncio)
                VALUES (?, ?)
            ");

            $stmt->execute([$idUtente, $idAnnuncio]);

            header("Location: index.php?action=carrello");
            exit;

        } catch (PDOException $e) {
            echo "Errore: annuncio già presente nel carrello";
        }
    }

    public function lista(int $idUtente): void
    {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT a.*
            FROM Carrello c
            JOIN Annuncio a ON c.id_annuncio = a.id_annuncio
            WHERE c.id_utente = ?
        ");

        $stmt->execute([$idUtente]);

        $annunciCarrello = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/carrello/lista.php';
    }
}
