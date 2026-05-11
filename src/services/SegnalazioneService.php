<?php

require_once __DIR__ . '/BaseService.php';

class SegnalazioneService extends BaseService
{
    public function crea(array $data, int $idSegnalante): int
    {
        $this->requirePositiveId($idSegnalante, 'Segnalante');

        $tipologia = $this->clean($data['tipologia'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');

        $idAnnuncio = $this->nullablePositiveInt($data['id_annuncio'] ?? null);
        $idUtenteSegnalato = $this->nullablePositiveInt($data['id_utente_segnalato'] ?? null);
        $idBusiness = $this->nullablePositiveInt($data['id_business'] ?? null);
        $idFeedback = $this->nullablePositiveInt($data['id_feedback'] ?? null);

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
            SELECT s.*, u.username AS segnalante_username
            FROM segnalazione s
            JOIN utente_registrato u ON u.id_utente = s.id_segnalante
            ORDER BY s.data_segnalazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function chiudi(int $idSegnalazione): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');

        $stmt = $this->db->prepare("
            UPDATE segnalazione
            SET stato = 'Risolta', data_risoluzione = CURRENT_TIMESTAMP
            WHERE id_segnalazione = ?
        ");
        $stmt->execute([$idSegnalazione]);
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
}
