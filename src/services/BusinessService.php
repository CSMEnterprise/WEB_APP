<?php

require_once __DIR__ . '/BaseService.php';

class BusinessService extends BaseService
{
    public function findByUserId(int $idUtente): ?array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT ab.*, i.via, i.numero, i.cap, i.citta, i.provincia, i.paese
            FROM account_business ab
            LEFT JOIN indirizzi i ON i.id_business = ab.id_acc_business AND i.predefinito = 1
            WHERE ab.id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetch() ?: null;
    }

    public function creaAccount(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nomeAzienda    = $this->clean($data['nome_azienda'] ?? '');
        $pIva           = $this->clean($data['p_iva'] ?? $data['partita_iva'] ?? '');
        $emailAziendale = $this->clean($data['email_aziendale'] ?? '');
        $telefono       = $this->clean($data['telefono'] ?? '');
        $descrizione    = $this->clean($data['descrizione'] ?? '');

        // Campi indirizzo separati
        $via        = $this->clean($data['via'] ?? '');
        $numero     = $this->clean($data['numero'] ?? '');
        $cap        = $this->clean($data['cap'] ?? '');
        $citta      = $this->clean($data['citta'] ?? '');
        $provincia  = $this->clean($data['provincia'] ?? '');
        $paese      = $this->clean($data['paese'] ?? 'Italia');

        if ($nomeAzienda === '' || $pIva === '' || $emailAziendale === '') {
            throw new ServiceException('Nome azienda, partita IVA ed email aziendale sono obbligatori.');
        }

        try {
            // 1. Inserisci il business (senza indirizzo)
            $stmt = $this->db->prepare("
                INSERT INTO account_business
                    (id_utente, p_iva, nome_azienda, email_aziendale, telefono, descrizione)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $idUtente,
                $pIva,
                $nomeAzienda,
                $emailAziendale,
                $telefono    !== '' ? $telefono    : null,
                $descrizione !== '' ? $descrizione : null,
            ]);

            $idBusiness = $this->lastInsertId();

            // 2. Se è stato fornito almeno via e città, inserisci l'indirizzo
            if ($via !== '' && $citta !== '') {
                $stmtInd = $this->db->prepare("
                    INSERT INTO indirizzi
                        (id_business, tipo, via, numero, cap, citta, provincia, paese, predefinito)
                    VALUES (?, 'lavoro', ?, ?, ?, ?, ?, ?, 1)
                ");
                $stmtInd->execute([
                    $idBusiness,
                    $via,
                    $numero   !== '' ? $numero   : null,
                    $cap      !== '' ? $cap      : null,
                    $citta,
                    $provincia !== '' ? $provincia : null,
                    $paese,
                ]);
            }

        } catch (PDOException $e) {
            throw new ServiceException('Account business già esistente o dati già utilizzati.');
        }

        return $idBusiness;
    }

    public function aggiornaIndirizzo(int $idBusiness, array $data): void
    {
        $this->requirePositiveId($idBusiness, 'Business');

        $via       = $this->clean($data['via'] ?? '');
        $numero    = $this->clean($data['numero'] ?? '');
        $cap       = $this->clean($data['cap'] ?? '');
        $citta     = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese     = $this->clean($data['paese'] ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e città sono obbligatori.');
        }

        // Upsert: aggiorna se esiste già, inserisce se non c'è
        $stmt = $this->db->prepare("
            INSERT INTO indirizzi
                (id_business, tipo, via, numero, cap, citta, provincia, paese, predefinito)
            VALUES (?, 'lavoro', ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                via       = VALUES(via),
                numero    = VALUES(numero),
                cap       = VALUES(cap),
                citta     = VALUES(citta),
                provincia = VALUES(provincia),
                paese     = VALUES(paese)
        ");
        $stmt->execute([
            $idBusiness,
            $via,
            $numero    !== '' ? $numero    : null,
            $cap       !== '' ? $cap       : null,
            $citta,
            $provincia !== '' ? $provincia : null,
            $paese,
        ]);
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