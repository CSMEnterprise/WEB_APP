<?php

namespace App\Foundation;

use App\Entity\EModera;

/**
 * Repository dello storico delle azioni di moderazione.
 */
class FModera extends FBaseTable
{
    /**
     * Metadati usati da FBaseTable per CRUD generico.
     */
    protected function tableName(): string { return 'modera'; }
    protected function primaryKey(): string { return 'id_moderazione'; }
    protected function entityClass(): string { return EModera::class; }
    protected function columns(): array
    {
        return [
            'id_moderazione',
            'id_admin',
            'id_utente',
            'id_feedback',
            'id_annuncio',
            'id_business',
            'azione_compiuta',
            'data_azione',
        ];
    }

    public function create(EModera $moderazione): int
    {
        // Registra quale admin ha compiuto l'azione e su quale eventuale oggetto.
        return $this->insert([
            'id_admin' => $moderazione->getIdAdmin(),
            'id_utente' => $moderazione->getIdUtente(),
            'id_feedback' => $moderazione->getIdFeedback(),
            'id_annuncio' => $moderazione->getIdAnnuncio(),
            'id_business' => $moderazione->getIdBusiness(),
            'azione_compiuta' => $moderazione->getAzioneCompiuta(),
        ]);
    }

    public function byAdmin(int $idAdmin): array
    {
        // Storico personale mostrato nella dashboard dell'admin.
        return $this->fetchEntities(
            'SELECT * FROM `modera` WHERE `id_admin` = ? ORDER BY `data_azione` DESC, `id_moderazione` DESC',
            [$idAdmin]
        );
    }

    public function withAdminDetails(array $filters = []): array
    {
        // Vista di audit: arricchisce l'azione con email e livello sicurezza admin.
        $adminSearch = trim((string) ($filters['admin'] ?? ''));
        $where = [];
        $params = [];

        if ($adminSearch !== '') {
            $where[] = "(a.`email` LIKE CONCAT('%', ?, '%') OR m.`id_admin` = ?)";
            $params[] = $adminSearch;
            $params[] = ctype_digit($adminSearch) ? (int) $adminSearch : 0;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        return $this->fetchEntities("
            SELECT
                m.*,
                a.`email` AS admin_email,
                a.`livello_sicurezza`
            FROM `modera` m
            JOIN `admin` a ON a.`id_admin` = m.`id_admin`
            {$whereSql}
            ORDER BY m.`data_azione` DESC, m.`id_moderazione` DESC
        ", $params);
    }
}
