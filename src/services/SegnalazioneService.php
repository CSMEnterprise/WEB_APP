<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../Entity/ESegnalazione.php';

class SegnalazioneService extends BaseService
{
    public function crea(array $data, int $idSegnalante): int
    {
        $segnalazione = ESegnalazione::fromArray(array_merge($data, [
            'id_segnalante' => $idSegnalante,
            'id_annuncio' => $this->nullablePositiveInt($data['id_annuncio'] ?? null),
            'id_utente_segnalato' => $this->nullablePositiveInt($data['id_utente_segnalato'] ?? null),
            'id_business' => $this->nullablePositiveInt($data['id_business'] ?? null),
            'id_feedback' => $this->nullablePositiveInt($data['id_feedback'] ?? null),
        ]));

        return $this->creaDaEntity($segnalazione);
    }

    public function creaDaEntity(ESegnalazione $segnalazione): int
    {
        $idSegnalante = $segnalazione->getIdSegnalante();
        $tipologia = $this->clean($segnalazione->getTipologia());
        $descrizione = $this->clean($segnalazione->getDescrizione());

        $idAnnuncio = $this->nullablePositiveInt($segnalazione->getIdAnnuncio());
        $idUtenteSegnalato = $this->nullablePositiveInt($segnalazione->getIdUtenteSegnalato());
        $idBusiness = $this->nullablePositiveInt($segnalazione->getIdBusiness());
        $idFeedback = $this->nullablePositiveInt($segnalazione->getIdFeedback());

        $this->requirePositiveId($idSegnalante, 'Segnalante');

        $targets = array_filter([$idAnnuncio, $idUtenteSegnalato, $idBusiness, $idFeedback], fn($v) => $v !== null);

        if ($tipologia === '' || !in_array($tipologia, ['Spam', 'Truffa', 'Contenuto_inappropriato', 'Altro'], true)) {
            throw new ServiceException('Tipologia segnalazione non valida.');
        }

        if (count($targets) !== 1) {
            throw new ServiceException('Devi segnalare esattamente un elemento.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO segnalazione
            (id_segnalante, id_annuncio, id_utente_segnalato, id_business, id_feedback, tipologia, descrizione, stato)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Aperta')
        ");

        $stmt->execute([
            $idSegnalante,
            $idAnnuncio,
            $idUtenteSegnalato,
            $idBusiness,
            $idFeedback,
            $tipologia,
            $descrizione !== '' ? $descrizione : null
        ]);

        return $this->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                s.*,
                u.username                     AS segnalante_username,
                a.titolo                       AS annuncio_titolo,
                us.username                    AS utente_segnalato_username,
                ab.nome_azienda                AS business_nome,
                f.id_feedback                  AS feedback_id
            FROM segnalazione s
            JOIN utente_registrato u       ON u.id_utente            = s.id_segnalante
            LEFT JOIN annuncio a           ON a.id_annuncio           = s.id_annuncio
            LEFT JOIN utente_registrato us ON us.id_utente            = s.id_utente_segnalato
            LEFT JOIN account_business ab  ON ab.id_acc_business      = s.id_business
            LEFT JOIN feedback f           ON f.id_feedback            = s.id_feedback
            ORDER BY s.data_segnalazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function getAllEntity(): array
    {
        return $this->toSegnalazioneEntities($this->getAll());
    }

    public function getFiltrate(array $filters = []): array
    {
        $oggetto = $this->clean($filters['oggetto'] ?? '');
        $tipologia = $this->clean($filters['tipologia'] ?? '');

        $where = [];
        $params = [];

        $oggettiConsentiti = ['annuncio', 'utente', 'business', 'feedback'];
        if (in_array($oggetto, $oggettiConsentiti, true)) {
            $where[] = match ($oggetto) {
                'annuncio' => 's.id_annuncio IS NOT NULL',
                'utente' => 's.id_utente_segnalato IS NOT NULL',
                'business' => 's.id_business IS NOT NULL',
                'feedback' => 's.id_feedback IS NOT NULL',
            };
        }

        $tipologieConsentite = ['Spam', 'Truffa', 'Contenuto_inappropriato', 'Altro'];
        if (in_array($tipologia, $tipologieConsentite, true)) {
            $where[] = 's.tipologia = ?';
            $params[] = $tipologia;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT
                s.*,
                u.username                     AS segnalante_username,
                a.titolo                       AS annuncio_titolo,
                us.username                    AS utente_segnalato_username,
                ab.nome_azienda                AS business_nome,
                f.id_feedback                  AS feedback_id
            FROM segnalazione s
            JOIN utente_registrato u       ON u.id_utente            = s.id_segnalante
            LEFT JOIN annuncio a           ON a.id_annuncio           = s.id_annuncio
            LEFT JOIN utente_registrato us ON us.id_utente            = s.id_utente_segnalato
            LEFT JOIN account_business ab  ON ab.id_acc_business      = s.id_business
            LEFT JOIN feedback f           ON f.id_feedback            = s.id_feedback
            {$whereSql}
            ORDER BY s.data_segnalazione DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getFiltrateEntity(array $filters = []): array
    {
        return $this->toSegnalazioneEntities($this->getFiltrate($filters));
    }

    public function chiudi(int $idSegnalazione, int $idAdmin): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');
        $this->requirePositiveId($idAdmin, 'Admin');

        $stmt = $this->db->prepare("
            UPDATE segnalazione
            SET stato = 'Risolta',
                id_admin = ?,
                data_risoluzione = CURRENT_TIMESTAMP
            WHERE id_segnalazione = ?
        ");
        $stmt->execute([$idAdmin, $idSegnalazione]);
    }

    public function elimina(int $idSegnalazione): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');

        $stmt = $this->db->prepare("
            DELETE FROM segnalazione
            WHERE id_segnalazione = ?
        ");
        $stmt->execute([$idSegnalazione]);
    }

    private function nullablePositiveInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    private function toSegnalazioneEntities(array $segnalazioni): array
    {
        return array_map(static fn(array $segnalazione) => ESegnalazione::fromArray($segnalazione), $segnalazioni);
    }
}
