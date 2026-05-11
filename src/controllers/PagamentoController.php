<?php

class PagamentoController
{
    public function crea(int $idUtente, int $idAnnuncio): void
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT prezzo
                FROM Annuncio
                WHERE id_annuncio = ?
            ");

            $stmt->execute([$idAnnuncio]);

            $annuncio = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$annuncio) {
                throw new Exception("Annuncio non trovato");
            }

            $stmt = $pdo->prepare("
                INSERT INTO Pagamento
                (id_utente, id_annuncio, importo, stato)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $idUtente,
                $idAnnuncio,
                $annuncio['prezzo'],
                'in_attesa'
            ]);

            $pdo->commit();

            echo "Pagamento creato correttamente. Stato: in attesa";

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Errore pagamento: " . $e->getMessage();
        }
    }
}
