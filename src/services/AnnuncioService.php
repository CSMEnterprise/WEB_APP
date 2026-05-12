<?php

require_once __DIR__ . '/BaseService.php';

class AnnuncioService extends BaseService
{
    public function getAnnunciAttivi(): array
    {
        $stmt = $this->db->query("
            SELECT a.*, c.nome AS categoria_nome, u.username AS venditore_username
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
            ORDER BY a.data_creazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function findById(int $idAnnuncio): ?array
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            SELECT a.*, c.nome AS categoria_nome, u.username AS venditore_username
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.id_annuncio = ?
            LIMIT 1
        ");
        $stmt->execute([$idAnnuncio]);

        return $stmt->fetch() ?: null;
    }

    public function getByUserId(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT *
            FROM annuncio
            WHERE id_utente = ?
            ORDER BY data_creazione DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
    }

    public function crea(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $titolo = $this->clean($data['titolo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');
        $idCategoria = (int) ($data['id_categoria'] ?? 0);
        $statoConservazione = $this->clean($data['stato_conservazione'] ?? '');
        $prezzo = (float) ($data['prezzo'] ?? 0);
        $modalitaConsegna = $this->clean($data['modalita_consegna'] ?? '');

        if ($titolo === '' || $idCategoria <= 0 || $statoConservazione === '' || $prezzo <= 0 || $modalitaConsegna === '') {
            throw new ServiceException('Compila tutti i campi obbligatori dell’annuncio.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO annuncio
            (id_utente, id_categoria, titolo, descrizione, stato_conservazione, prezzo, modalita_consegna, stato)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'attivo')
        ");

        $stmt->execute([
            $idUtente,
            $idCategoria,
            $titolo,
            $descrizione !== '' ? $descrizione : null,
            $statoConservazione,
            $prezzo,
            $modalitaConsegna
        ]);

        return $this->lastInsertId();
    }

    public function elimina(int $idAnnuncio, int $idUtente): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            DELETE FROM annuncio
            WHERE id_annuncio = ? AND id_utente = ?
        ");
        $stmt->execute([$idAnnuncio, $idUtente]);

        if ($stmt->rowCount() === 0) {
            throw new ServiceException('Non puoi eliminare questo annuncio.');
        }
    }

    public function eliminaDaAdmin(int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            DELETE FROM annuncio
            WHERE id_annuncio = ?
        ");
        $stmt->execute([$idAnnuncio]);
    }

    public function marcaVenduto(int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            UPDATE annuncio
            SET stato = 'venduto'
            WHERE id_annuncio = ?
        ");
        $stmt->execute([$idAnnuncio]);
    }
}
