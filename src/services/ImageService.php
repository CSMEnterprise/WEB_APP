<?php

require_once __DIR__ . '/BaseService.php';

class ImageService extends BaseService
{
    public function findByAnnuncio(int $annuncioId): array
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->fetchAll(
            'SELECT * FROM immagine WHERE id_annuncio = :id ORDER BY ordine ASC, id_immagine ASC',
            [':id' => $annuncioId]
        );
    }

    public function add(int $annuncioId, string $url, int $ordine = 0): int
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');
        $this->requireNotEmpty($url, 'url immagine');

        $this->execute(
            'INSERT INTO immagine (id_annuncio, url, ordine)
             VALUES (:id_annuncio, :url, :ordine)',
            [
                ':id_annuncio' => $annuncioId,
                ':url' => trim($url),
                ':ordine' => $ordine,
            ]
        );

        return $this->lastInsertId();
    }

    public function delete(int $imageId): bool
    {
        $this->requirePositiveInt($imageId, 'id_immagine');

        return $this->execute(
            'DELETE FROM immagine WHERE id_immagine = :id',
            [':id' => $imageId]
        );
    }
}
