<?php

require_once __DIR__ . '/BaseService.php';

class BusinessService extends BaseService
{
    public function findByUserId(int $idUtente): ?array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT *
            FROM account_business
            WHERE id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetch() ?: null;
    }

    public function creaAccount(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nomeAzienda = $this->clean($data['nome_azienda'] ?? '');
        $pIva = $this->clean($data['p_iva'] ?? $data['partita_iva'] ?? '');
        $emailAziendale = $this->clean($data['email_aziendale'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');
        $indirizzo = $this->clean($data['indirizzo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');

        if ($nomeAzienda === '' || $pIva === '' || $emailAziendale === '') {
            throw new ServiceException('Nome azienda, partita IVA ed email aziendale sono obbligatori.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO account_business
            (id_utente, p_iva, nome_azienda, email_aziendale, telefono, indirizzo, descrizione)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        try {
            $stmt->execute([
                $idUtente,
                $pIva,
                $nomeAzienda,
                $emailAziendale,
                $telefono !== '' ? $telefono : null,
                $indirizzo !== '' ? $indirizzo : null,
                $descrizione !== '' ? $descrizione : null
            ]);
        } catch (PDOException $e) {
            throw new ServiceException('Account business già esistente o dati già utilizzati.');
        }

        return $this->lastInsertId();
    }

    public function getOrdiniRicevuti(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT p.*, a.titolo
            FROM pagamento p
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            WHERE a.id_utente = ?
            ORDER BY p.data DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
    }
}
