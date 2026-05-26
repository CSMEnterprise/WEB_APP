<?php

namespace App\Foundation;

use App\Entity\EAdmin;

class FAdmin extends FBaseTable
{
    protected function tableName(): string { return 'admin'; }
    protected function primaryKey(): string { return 'id_admin'; }
    protected function entityClass(): string { return EAdmin::class; }
    protected function columns(): array
    {
        return ['id_admin', 'email', 'password_hash', 'livello_sicurezza', 'stato_ban', 'data_creazione'];
    }

    public function allOrdered(): array
    {
        $statoBanSelect = $this->hasColumn('stato_ban') ? '`stato_ban`' : '0 AS stato_ban';

        return $this->fetchEntities("
            SELECT `id_admin`, `email`, `livello_sicurezza`, {$statoBanSelect}, `data_creazione`
            FROM `admin`
            ORDER BY `livello_sicurezza` DESC, `data_creazione` DESC
        ");
    }

    public function findForModeration(int $idAdmin): ?EAdmin
    {
        $statoBanSelect = $this->hasColumn('stato_ban') ? '`stato_ban`' : '0 AS stato_ban';

        $entity = $this->fetchEntity("
            SELECT `id_admin`, `email`, `livello_sicurezza`, {$statoBanSelect}
            FROM `admin`
            WHERE `id_admin` = ?
            LIMIT 1
        ", [$idAdmin]);

        return $entity instanceof EAdmin ? $entity : null;
    }

    public function setBanState(int $idAdmin, bool $banned): void
    {
        if (!$this->hasColumn('stato_ban')) {
            return;
        }

        $this->execute(
            'UPDATE `admin` SET `stato_ban` = ? WHERE `id_admin` = ?',
            [$banned ? 1 : 0, $idAdmin]
        );
    }
}
