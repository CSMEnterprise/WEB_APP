<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

class PreferitoService extends BaseService
{
    public function add(int $userId, int $annuncioId): bool
    {
        $this->requirePositiveInt($userId, 'id_utente');
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->execute(
            'INSERT IGNORE INTO preferito (id_utente, id_annuncio)
             VALUES (:id_utente, :id_annuncio)',
            [
                ':id_utente' => $userId,
                ':id_annuncio' => $annuncioId,
            ]
        );
    }

    public function remove(int $userId, int $annuncioId): bool
    {
        return $this->execute(
            'DELETE FROM preferito WHERE id_utente = :id_utente AND id_annuncio = :id_annuncio',
            [
                ':id_utente' => $userId,
                ':id_annuncio' => $annuncioId,
            ]
        );
    }

    public function toggle(int $userId, int $annuncioId): bool
    {
        if ($this->exists($userId, $annuncioId)) {
            $this->remove($userId, $annuncioId);
            return false;
        }

        $this->add($userId, $annuncioId);
        return true;
    }

    public function exists(int $userId, int $annuncioId): bool
    {
        return $this->fetchOne(
            'SELECT id_utente FROM preferito WHERE id_utente = :id_utente AND id_annuncio = :id_annuncio LIMIT 1',
            [
                ':id_utente' => $userId,
                ':id_annuncio' => $annuncioId,
            ]
        ) !== null;
    }

    public function byUser(int $userId): array
    {
        $this->requirePositiveInt($userId, 'id_utente');

        return $this->fetchAll(
            'SELECT p.*, a.titolo, a.prezzo, a.stato, img.url AS immagine_principale
             FROM preferito p
             INNER JOIN annuncio a ON a.id_annuncio = p.id_annuncio
             LEFT JOIN immagine img ON img.id_annuncio = a.id_annuncio AND img.ordine = 0
             WHERE p.id_utente = :id_utente
             ORDER BY p.data_aggiunta DESC',
            [':id_utente' => $userId]
        );
    }
}
