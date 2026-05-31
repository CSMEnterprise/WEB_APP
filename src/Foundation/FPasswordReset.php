<?php

namespace App\Foundation;

use App\Entity\EPasswordReset;

/**
 * Repository dei token monouso per recupero password.
 */
class FPasswordReset extends FBaseTable
{
    protected function tableName(): string
    {
        return 'password_reset';
    }

    protected function primaryKey(): string
    {
        return 'id_reset';
    }

    protected function entityClass(): string
    {
        return EPasswordReset::class;
    }

    protected function columns(): array
    {
        return [
            'id_reset',
            'id_utente',
            'token',
            'scadenza',
            'usato',
            'creato_il',
        ];
    }

    public function invalidateForUser(int $idUtente): void
    {
        $this->execute(
            'UPDATE `password_reset` SET `usato` = 1 WHERE `id_utente` = ?',
            [$idUtente]
        );
    }

    public function createForUser(int $idUtente, string $token, string $scadenza): int
    {
        return $this->insert([
            'id_utente' => $idUtente,
            'token' => $token,
            'scadenza' => $scadenza,
            'usato' => 0,
        ]);
    }

    public function findUsableByToken(string $token): ?EPasswordReset
    {
        $reset = $this->fetchEntity("
            SELECT `id_reset`, `id_utente`, `token`, `scadenza`, `usato`, `creato_il`
            FROM `password_reset`
            WHERE `token` = ? AND `usato` = 0
            LIMIT 1
        ", [$token]);

        return $reset instanceof EPasswordReset ? $reset : null;
    }

    public function userIdByValidToken(string $token): int
    {
        return (int) $this->fetchColumn("
            SELECT `id_utente`
            FROM `password_reset`
            WHERE `token` = ? AND `usato` = 0 AND `scadenza` > NOW()
            LIMIT 1
        ", [$token]);
    }

    public function markTokenUsed(string $token): void
    {
        $this->execute(
            'UPDATE `password_reset` SET `usato` = 1 WHERE `token` = ?',
            [$token]
        );
    }
}
