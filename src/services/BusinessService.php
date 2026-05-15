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

        $via        = $this->clean($data['via'] ?? '');
        $numero     = $this->clean($data['numero'] ?? '');
        $cap        = $this->clean($data['cap'] ?? '');
        $citta      = $this->clean($data['citta'] ?? '');
        $provincia  = $this->clean($data['provincia'] ?? '');
        $paese      = $this->clean($data['paese'] ?? 'Italia');

        if ($nomeAzienda === '' || $pIva === '' || $emailAziendale === '') {
            throw new ServiceException('Nome azienda, partita IVA ed email aziendale sono obbligatori.');
        }

        if (!preg_match('/^[\p{L}0-9 .&\'-]{2,80}$/u', $nomeAzienda)) {
            throw new ServiceException('Il nome azienda deve contenere 2-80 caratteri validi.');
        }

        if (!preg_match('/^[0-9]{11}$/', $pIva)) {
            throw new ServiceException('La partita IVA deve contenere esattamente 11 cifre.');
        }

        if (!filter_var($emailAziendale, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email aziendale non valida.');
        }

        if ($telefono !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e può iniziare con +.');
        }

        if ($cap !== '' && !preg_match('/^[0-9]{5}$/', $cap)) {
            throw new ServiceException('Il CAP deve contenere esattamente 5 cifre.');
        }

        if ($provincia !== '' && !preg_match('/^[A-Za-z]{2}$/', $provincia)) {
            throw new ServiceException('La provincia deve contenere 2 lettere.');
        }

        if ($citta !== '' && !preg_match('/^[\p{L} .\'-]{2,80}$/u', $citta)) {
            throw new ServiceException('La città deve contenere 2-80 caratteri validi.');
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO account_business
                    (id_utente, p_iva, nome_azienda, email_aziendale, telefono)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $idUtente,
                $pIva,
                $nomeAzienda,
                $emailAziendale,
                $telefono    !== '' ? $telefono    : null,
            ]);

            $idBusiness = $this->lastInsertId();

            if ($via !== '' || $citta !== '') {
                if ($via === '' || $citta === '') {
                    throw new ServiceException('Per salvare la sede aziendale devi indicare almeno via e città.');
                }

                $stmtInd = $this->db->prepare("
                    INSERT INTO indirizzi
                        (id_business, via, numero, cap, citta, provincia, paese, predefinito)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                ");
                $stmtInd->execute([
                    $idBusiness,
                    $via,
                    $numero    !== '' ? $numero    : null,
                    $cap       !== '' ? $cap       : null,
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

        $stmt = $this->db->prepare("
            DELETE FROM indirizzi
            WHERE id_business = ? AND predefinito = 1
        ");
        $stmt->execute([$idBusiness]);

        $stmt = $this->db->prepare("
            INSERT INTO indirizzi
                (id_business, via, numero, cap, citta, provincia, paese, predefinito)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
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
