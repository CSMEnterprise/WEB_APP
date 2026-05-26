<?php

namespace App\Foundation;

use App\Entity\ESegnalazione;

class FSegnalazione extends FBaseTable
{
    protected function tableName(): string { return 'segnalazione'; }
    protected function primaryKey(): string { return 'id_segnalazione'; }
    protected function entityClass(): string { return ESegnalazione::class; }
    protected function columns(): array
    {
        return [
            'id_segnalazione',
            'id_segnalante',
            'id_annuncio',
            'id_utente_segnalato',
            'id_business',
            'id_feedback',
            'tipologia',
            'descrizione',
            'stato',
            'data_segnalazione',
            'id_admin',
            'data_risoluzione',
        ];
    }

    public function create(ESegnalazione $segnalazione): int
    {
        return $this->insert([
            'id_segnalante' => $segnalazione->getIdSegnalante(),
            'id_annuncio' => $segnalazione->getIdAnnuncio(),
            'id_utente_segnalato' => $segnalazione->getIdUtenteSegnalato(),
            'id_business' => $segnalazione->getIdBusiness(),
            'id_feedback' => $segnalazione->getIdFeedback(),
            'tipologia' => $segnalazione->getTipologia(),
            'descrizione' => $segnalazione->getDescrizione(),
            'stato' => $segnalazione->getStato(),
        ]);
    }

    public function allWithDetails(array $filters = []): array
    {
        $oggetto = trim((string) ($filters['oggetto'] ?? ''));
        $tipologia = trim((string) ($filters['tipologia'] ?? ''));
        $where = [];
        $params = [];

        $oggettiConsentiti = ['annuncio', 'utente', 'business', 'feedback'];
        if (in_array($oggetto, $oggettiConsentiti, true)) {
            $where[] = match ($oggetto) {
                'annuncio' => 's.`id_annuncio` IS NOT NULL',
                'utente' => 's.`id_utente_segnalato` IS NOT NULL',
                'business' => 's.`id_business` IS NOT NULL',
                'feedback' => 's.`id_feedback` IS NOT NULL',
            };
        }

        $tipologieConsentite = ['Spam', 'Truffa', 'Contenuto_inappropriato', 'Altro'];
        if (in_array($tipologia, $tipologieConsentite, true)) {
            $where[] = 's.`tipologia` = ?';
            $params[] = $tipologia;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        return $this->fetchEntities("
            SELECT
                s.*,
                u.`username` AS segnalante_username,
                a.`titolo` AS annuncio_titolo,
                us.`username` AS utente_segnalato_username,
                ab.`nome_azienda` AS business_nome,
                f.`id_feedback` AS feedback_id
            FROM `segnalazione` s
            JOIN `utente_registrato` u ON u.`id_utente` = s.`id_segnalante`
            LEFT JOIN `annuncio` a ON a.`id_annuncio` = s.`id_annuncio`
            LEFT JOIN `utente_registrato` us ON us.`id_utente` = s.`id_utente_segnalato`
            LEFT JOIN `account_business` ab ON ab.`id_acc_business` = s.`id_business`
            LEFT JOIN `feedback` f ON f.`id_feedback` = s.`id_feedback`
            {$whereSql}
            ORDER BY s.`data_segnalazione` DESC
        ", $params);
    }

    public function close(int $idSegnalazione, int $idAdmin): void
    {
        $this->execute("
            UPDATE `segnalazione`
            SET `stato` = 'Risolta',
                `id_admin` = ?,
                `data_risoluzione` = CURRENT_TIMESTAMP
            WHERE `id_segnalazione` = ?
        ", [$idAdmin, $idSegnalazione]);
    }
}
