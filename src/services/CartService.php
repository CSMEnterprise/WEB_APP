<?php

require_once __DIR__ . '/BaseService.php';

class CartService extends BaseService
{
    public function getOrCreateCartId(int $userId): int
    {
        $this->requirePositiveInt($userId, 'id_utente');

        $cart = $this->fetchOne(
            'SELECT id_carrello FROM carrello WHERE id_utente = :id_utente LIMIT 1',
            [':id_utente' => $userId]
        );

        if ($cart) {
            return (int) $cart['id_carrello'];
        }

        $this->execute(
            'INSERT INTO carrello (id_utente) VALUES (:id_utente)',
            [':id_utente' => $userId]
        );

        return $this->lastInsertId();
    }

    public function addItem(int $userId, int $annuncioId): bool
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');
        $cartId = $this->getOrCreateCartId($userId);

        $annuncio = $this->fetchOne(
            "SELECT id_annuncio, stato FROM annuncio WHERE id_annuncio = :id LIMIT 1",
            [':id' => $annuncioId]
        );

        if (!$annuncio || $annuncio['stato'] !== 'attivo') {
            throw new ServiceException('Annuncio non disponibile.');
        }

        return $this->execute(
            'INSERT IGNORE INTO elemento_carrello (id_carrello, id_annuncio)
             VALUES (:id_carrello, :id_annuncio)',
            [
                ':id_carrello' => $cartId,
                ':id_annuncio' => $annuncioId,
            ]
        );
    }

    public function items(int $userId): array
    {
        $cartId = $this->getOrCreateCartId($userId);

        return $this->fetchAll(
            'SELECT ec.*, a.titolo, a.prezzo, a.stato, a.modalita_consegna,
                    img.url AS immagine_principale
             FROM elemento_carrello ec
             INNER JOIN annuncio a ON a.id_annuncio = ec.id_annuncio
             LEFT JOIN immagine img ON img.id_annuncio = a.id_annuncio AND img.ordine = 0
             WHERE ec.id_carrello = :id_carrello
             ORDER BY ec.data_aggiunta DESC',
            [':id_carrello' => $cartId]
        );
    }

    public function removeItem(int $userId, int $annuncioId): bool
    {
        $cartId = $this->getOrCreateCartId($userId);

        return $this->execute(
            'DELETE FROM elemento_carrello
             WHERE id_carrello = :id_carrello AND id_annuncio = :id_annuncio',
            [
                ':id_carrello' => $cartId,
                ':id_annuncio' => $annuncioId,
            ]
        );
    }

    public function clear(int $userId): bool
    {
        $cartId = $this->getOrCreateCartId($userId);

        return $this->execute(
            'DELETE FROM elemento_carrello WHERE id_carrello = :id_carrello',
            [':id_carrello' => $cartId]
        );
    }

    public function total(int $userId): float
    {
        $cartId = $this->getOrCreateCartId($userId);
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(a.prezzo), 0) AS totale
             FROM elemento_carrello ec
             INNER JOIN annuncio a ON a.id_annuncio = ec.id_annuncio
             WHERE ec.id_carrello = :id_carrello AND a.stato = 'attivo'",
            [':id_carrello' => $cartId]
        );

        return (float) ($row['totale'] ?? 0);
    }
}
