<?php

namespace App\Foundation;

use App\Entity\EUtenteRegistrato;

/**
 * Repository degli utenti registrati.
 * Include ricerche pubbliche, profilo e strumenti di moderazione admin.
 */
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
        // Il profilo utente include l'indirizzo predefinito tramite LEFT JOIN.
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

    public function findByEmailForLogin(string $email): ?EUtenteRegistrato
    {
        $entity = $this->fetchEntity("
            SELECT u.`id_utente`, u.`email`, u.`email_verificata`, u.`token_verifica`, u.`token_scadenza`,
                   u.`username`, u.`password_hash`, u.`nome`, u.`telefono`, u.`propic`,
                   u.`stato_ban`, u.`data_registrazione`,
                   ab.`id_acc_business`, ab.`nome_azienda`
            FROM `utente_registrato` u
            LEFT JOIN `account_business` ab ON ab.`id_utente` = u.`id_utente`
            WHERE u.`email` = ?
            LIMIT 1
        ", [$email]);

        return $entity instanceof EUtenteRegistrato ? $entity : null;
    }

    public function findBasicByEmail(string $email): ?EUtenteRegistrato
    {
        $entity = $this->fetchEntity("
            SELECT `id_utente`, `email`, `username`, `nome`
            FROM `utente_registrato`
            WHERE `email` = ?
            LIMIT 1
        ", [$email]);

        return $entity instanceof EUtenteRegistrato ? $entity : null;
    }

    public function findUnverifiedByEmail(string $email): ?EUtenteRegistrato
    {
        $entity = $this->fetchEntity("
            SELECT `id_utente`, `email`, `username`, `nome`
            FROM `utente_registrato`
            WHERE `email` = ? AND `email_verificata` = 0
            LIMIT 1
        ", [$email]);

        return $entity instanceof EUtenteRegistrato ? $entity : null;
    }

    public function findByVerificationToken(string $token): ?EUtenteRegistrato
    {
        $entity = $this->fetchEntity("
            SELECT `id_utente`, `email`, `email_verificata`, `token_verifica`, `token_scadenza`,
                   `username`, `password_hash`, `nome`, `telefono`, `propic`, `stato_ban`, `data_registrazione`
            FROM `utente_registrato`
            WHERE `token_verifica` = ? AND `email_verificata` = 0
            LIMIT 1
        ", [$token]);

        return $entity instanceof EUtenteRegistrato ? $entity : null;
    }

    public function createWithVerification(
        string $email,
        string $username,
        string $passwordHash,
        ?string $nome,
        ?string $telefono,
        string $token,
        string $scadenza
    ): int {
        return $this->insert([
            'email' => $email,
            'username' => $username,
            'password_hash' => $passwordHash,
            'nome' => $nome,
            'telefono' => $telefono,
            'email_verificata' => 0,
            'token_verifica' => $token,
            'token_scadenza' => $scadenza,
        ]);
    }

    public function confirmEmail(int $idUtente): void
    {
        $this->execute("
            UPDATE `utente_registrato`
            SET `email_verificata` = 1,
                `token_verifica` = NULL,
                `token_scadenza` = NULL
            WHERE `id_utente` = ?
        ", [$idUtente]);
    }

    public function updateVerificationToken(int $idUtente, string $token, string $scadenza): void
    {
        $this->execute("
            UPDATE `utente_registrato`
            SET `token_verifica` = ?, `token_scadenza` = ?
            WHERE `id_utente` = ?
        ", [$token, $scadenza, $idUtente]);
    }

    public function updatePasswordHash(int $idUtente, string $passwordHash): void
    {
        $this->execute(
            'UPDATE `utente_registrato` SET `password_hash` = ? WHERE `id_utente` = ?',
            [$passwordHash, $idUtente]
        );
    }

    public function updateProfile(int $idUtente, string $nome, ?string $telefono): void
    {
        // Aggiorna solo dati anagrafici modificabili dal profilo.
        $this->execute(
            'UPDATE `utente_registrato` SET `nome` = ?, `telefono` = ? WHERE `id_utente` = ?',
            [$nome, $telefono, $idUtente]
        );
    }

    public function updatePropic(int $idUtente, string $url): void
    {
        // Salva nel DB il path pubblico della nuova foto profilo.
        $this->execute(
            'UPDATE `utente_registrato` SET `propic` = ? WHERE `id_utente` = ?',
            [$url, $idUtente]
        );
    }

    public function updateName(int $idUtente, string $nome): void
    {
        // Usato quando l'indirizzo di spedizione fornisce anche il nome destinatario.
        $this->execute(
            'UPDATE `utente_registrato` SET `nome` = ? WHERE `id_utente` = ?',
            [$nome, $idUtente]
        );
    }

    public function searchPublic(string $q): array
    {
        // La ricerca pubblica non mostra account bannati e limita i risultati.
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
        // Ricerca ampia per pannello admin: id, email, username, nome o telefono.
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
        // Flag usato dai middleware/login per bloccare accesso e visibilita pubblica.
        $this->execute(
            'UPDATE `utente_registrato` SET `stato_ban` = ? WHERE `id_utente` = ?',
            [$banned ? 1 : 0, $idUtente]
        );
    }
}
