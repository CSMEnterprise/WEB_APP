<?php

namespace App\Foundation;

use App\Entity\EFeedback;

class FFeedback extends FBaseTable
{
    protected function tableName(): string { return 'feedback'; }
    protected function primaryKey(): string { return 'id_feedback'; }
    protected function entityClass(): string { return EFeedback::class; }
    protected function columns(): array
    {
        return ['id_feedback', 'id_autore', 'id_pagamento', 'valutazione', 'commento', 'data_feedback'];
    }

    public function create(EFeedback $feedback): int
    {
        return $this->insert([
            'id_autore' => $feedback->getIdAutore(),
            'id_pagamento' => $feedback->getIdPagamento(),
            'valutazione' => $feedback->getValutazione(),
            'commento' => $feedback->getCommento(),
        ]);
    }

    public function byPagamento(int $idPagamento): array
    {
        return $this->fetchEntities("
            SELECT f.*, u.`username` AS autore
            FROM `feedback` f
            JOIN `utente_registrato` u ON u.`id_utente` = f.`id_autore`
            WHERE f.`id_pagamento` = ?
            ORDER BY f.`data_feedback` DESC
        ", [$idPagamento]);
    }

    public function byUser(int $idUtente): array
    {
        return $this->fetchEntities("
            SELECT f.*, u.`username` AS autore, p.`id_acquirente`, a.`id_utente` AS venditore_id, a.`titolo`
            FROM `feedback` f
            JOIN `utente_registrato` u ON u.`id_utente` = f.`id_autore`
            JOIN `pagamento` p ON p.`id_pagamento` = f.`id_pagamento`
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE p.`id_acquirente` = ? OR a.`id_utente` = ?
            ORDER BY f.`data_feedback` DESC
        ", [$idUtente, $idUtente]);
    }

    public function byVenditore(int $idVenditore): array
    {
        return $this->fetchEntities("
            SELECT
                f.*,
                u.`username` AS autore,
                a.`titolo` AS annuncio_titolo,
                a.`id_annuncio` AS annuncio_id
            FROM `feedback` f
            JOIN `utente_registrato` u ON u.`id_utente` = f.`id_autore`
            JOIN `pagamento` p ON p.`id_pagamento` = f.`id_pagamento`
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE a.`id_utente` = ?
            ORDER BY f.`data_feedback` DESC
        ", [$idVenditore]);
    }

    public function existsForPagamentoAndAutore(int $idPagamento, int $idAutore): bool
    {
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM `feedback` WHERE `id_pagamento` = ? AND `id_autore` = ?',
            [$idPagamento, $idAutore]
        ) > 0;
    }

    public function averageForVenditore(int $idUtente): float
    {
        return (float) $this->fetchColumn("
            SELECT AVG(f.`valutazione`)
            FROM `feedback` f
            JOIN `pagamento` p ON p.`id_pagamento` = f.`id_pagamento`
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE a.`id_utente` = ?
        ", [$idUtente]);
    }
}
