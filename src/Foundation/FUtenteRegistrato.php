<?php

namespace App\Foundation;

use App\Entity\EUtenteRegistrato;

class FUtenteRegistrato extends FBaseTable
{
    protected function tableName(): string
    {
        return 'utente_registrato';
    }

    protected function primaryKey(): string
    {
        return 'id_utente';
    }

    protected function entityClass(): string
    {
        return EUtenteRegistrato::class;
    }

    protected function columns(): array
    {
        return [
            'id_utente',
            'email',
            'email_verificata',
            'token_verifica',
            'token_scadenza',
            'username',
            'password_hash',
            'nome',
            'telefono',
            'propic',
            'stato_ban',
            'data_registrazione',
        ];
    }

    public function findWithDefaultAddress(int $idUtente): ?EUtenteRegistrato
    {
        $entity = $this->fetchEntity("
            SELECT u.`id_utente`, u.`email`, u.`email_verificata`, u.`token_verifica`, u.`token_scadenza`,
                   u.`username`, u.`password_hash`, u.`nome`, u.`telefono`, u.`propic`,
                   u.`stato_ban`, u.`data_registrazione`,
                   i.`via`, i.`numero`, i.`cap`, i.`citta`, i.`provincia`, i.`paese`
            FROM `utente_registrato` u
            LEFT JOIN `indirizzi` i ON i.`id_utente` = u.`id_utente` AND i.`predefinito` = 1
            WHERE u.`id_utente` = ?
            LIMIT 1
        ", [$idUtente]);

        return $entity instanceof EUtenteRegistrato ? $entity : null;
    }

    public function updateProfile(int $idUtente, string $nome, ?string $telefono): void
    {
        $this->execute(
            'UPDATE `utente_registrato` SET `nome` = ?, `telefono` = ? WHERE `id_utente` = ?',
            [$nome, $telefono, $idUtente]
        );
    }

    public function updatePropic(int $idUtente, string $url): void
    {
        $this->execute(
            'UPDATE `utente_registrato` SET `propic` = ? WHERE `id_utente` = ?',
            [$url, $idUtente]
        );
    }

    public function updateName(int $idUtente, string $nome): void
    {
        $this->execute(
            'UPDATE `utente_registrato` SET `nome` = ? WHERE `id_utente` = ?',
            [$nome, $idUtente]
        );
    }

    public function searchPublic(string $q): array
    {
        if ($q === '') {
            return [];
        }

        return $this->fetchEntities("
            SELECT `id_utente`, `username`, `nome`, `propic`, `data_registrazione`
            FROM `utente_registrato`
            WHERE (`username` LIKE CONCAT('%', ?, '%')
               OR  `nome` LIKE CONCAT('%', ?, '%'))
              AND `stato_ban` = 0
            ORDER BY `username` ASC
            LIMIT 20
        ", [$q, $q]);
    }

    public function allForAdmin(string $q = ''): array
    {
        $where = '';
        $params = [];

        if ($q !== '') {
            $where = "
                WHERE `id_utente` = ?
                   OR `email` LIKE CONCAT('%', ?, '%')
                   OR `username` LIKE CONCAT('%', ?, '%')
                   OR `nome` LIKE CONCAT('%', ?, '%')
                   OR `telefono` LIKE CONCAT('%', ?, '%')
            ";
            $id = ctype_digit($q) ? (int) $q : 0;
            $params = [$id, $q, $q, $q, $q];
        }

        return $this->fetchEntities("
            SELECT `id_utente`, `email`, `username`, `nome`, `telefono`, `stato_ban`, `data_registrazione`
            FROM `utente_registrato`
            {$where}
            ORDER BY `data_registrazione` DESC
        ", $params);
    }

    public function setBanState(int $idUtente, bool $banned): void
    {
        $this->execute(
            'UPDATE `utente_registrato` SET `stato_ban` = ? WHERE `id_utente` = ?',
            [$banned ? 1 : 0, $idUtente]
        );
    }
}
