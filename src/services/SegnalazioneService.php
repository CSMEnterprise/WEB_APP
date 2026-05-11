<?php

require_once __DIR__ . '/BaseService.php';

class SegnalazioneService extends BaseService
{
    private const TIPOLOGIE = ['Spam', 'Truffa', 'Contenuto_inappropriato', 'Altro'];
    private const STATI = ['Aperta', 'In_revisione', 'Risolta'];

    public function create(int $reporterId, array $data): int
    {
        $this->requirePositiveInt($reporterId, 'id_segnalante');

        if (!in_array($data['tipologia'] ?? '', self::TIPOLOGIE, true)) {
            throw new ServiceException('Tipologia segnalazione non valida.');
        }

        if (empty($data['id_annuncio']) && empty($data['id_utente_segnalato']) && empty($data['id_business']) && empty($data['id_feedback'])) {
            throw new ServiceException('La segnalazione deve riferirsi ad almeno un oggetto.');
        }

        $this->execute(
            'INSERT INTO segnalazione
                (id_segnalante, id_annuncio, id_utente_segnalato, id_business, id_feedback, tipologia, descrizione)
             VALUES
                (:id_segnalante, :id_annuncio, :id_utente_segnalato, :id_business, :id_feedback, :tipologia, :descrizione)',
            [
                ':id_segnalante' => $reporterId,
                ':id_annuncio' => !empty($data['id_annuncio']) ? (int) $data['id_annuncio'] : null,
                ':id_utente_segnalato' => !empty($data['id_utente_segnalato']) ? (int) $data['id_utente_segnalato'] : null,
                ':id_business' => !empty($data['id_business']) ? (int) $data['id_business'] : null,
                ':id_feedback' => !empty($data['id_feedback']) ? (int) $data['id_feedback'] : null,
                ':tipologia' => $data['tipologia'],
                ':descrizione' => trim($data['descrizione'] ?? '') ?: null,
            ]
        );

        return $this->lastInsertId();
    }

    public function all(?string $status = null): array
    {
        $params = [];
        $where = '';

        if ($status !== null) {
            if (!in_array($status, self::STATI, true)) {
                throw new ServiceException('Stato segnalazione non valido.');
            }
            $where = 'WHERE s.stato = :stato';
            $params[':stato'] = $status;
        }

        return $this->fetchAll(
            "SELECT s.*, u.username AS segnalante_username
             FROM segnalazione s
             INNER JOIN utente_registrato u ON u.id_utente = s.id_segnalante
             {$where}
             ORDER BY s.data_segnalazione DESC",
            $params
        );
    }

    public function updateStatus(int $reportId, string $status, int $adminId): bool
    {
        $this->requirePositiveInt($reportId, 'id_segnalazione');
        $this->requirePositiveInt($adminId, 'id_admin');

        if (!in_array($status, self::STATI, true)) {
            throw new ServiceException('Stato segnalazione non valido.');
        }

        return $this->execute(
            'UPDATE segnalazione
             SET stato = :stato,
                 id_admin = :id_admin,
                 data_risoluzione = CASE WHEN :stato_case = "Risolta" THEN CURRENT_TIMESTAMP ELSE data_risoluzione END
             WHERE id_segnalazione = :id',
            [
                ':stato' => $status,
                ':stato_case' => $status,
                ':id_admin' => $adminId,
                ':id' => $reportId,
            ]
        );
    }
}
