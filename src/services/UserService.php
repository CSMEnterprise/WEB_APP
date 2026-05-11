<?php

require_once __DIR__ . '/BaseService.php';

class UserService extends BaseService
{
    public function findById(int $userId): ?array
    {
        $this->requirePositiveInt($userId, 'id_utente');

        $user = $this->fetchOne(
            'SELECT id_utente, email, username, nome, telefono, indirizzo, propic, stato_ban, data_registrazione
             FROM utente_registrato
             WHERE id_utente = :id',
            [':id' => $userId]
        );

        return $user;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $this->requirePositiveInt($userId, 'id_utente');

        return $this->execute(
            'UPDATE utente_registrato
             SET nome = :nome,
                 telefono = :telefono,
                 indirizzo = :indirizzo,
                 propic = :propic
             WHERE id_utente = :id',
            [
                ':nome' => trim($data['nome'] ?? '') ?: null,
                ':telefono' => trim($data['telefono'] ?? '') ?: null,
                ':indirizzo' => trim($data['indirizzo'] ?? '') ?: null,
                ':propic' => trim($data['propic'] ?? '') ?: null,
                ':id' => $userId,
            ]
        );
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $this->requirePositiveInt($userId, 'id_utente');

        if (strlen($newPassword) < 8) {
            throw new ServiceException('La nuova password deve contenere almeno 8 caratteri.');
        }

        $user = $this->fetchOne(
            'SELECT password_hash FROM utente_registrato WHERE id_utente = :id',
            [':id' => $userId]
        );

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            throw new ServiceException('Password attuale non corretta.');
        }

        return $this->execute(
            'UPDATE utente_registrato SET password_hash = :hash WHERE id_utente = :id',
            [
                ':hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                ':id' => $userId,
            ]
        );
    }

    public function ban(int $userId): bool
    {
        return $this->setBanStatus($userId, true);
    }

    public function unban(int $userId): bool
    {
        return $this->setBanStatus($userId, false);
    }

    private function setBanStatus(int $userId, bool $banned): bool
    {
        $this->requirePositiveInt($userId, 'id_utente');

        return $this->execute(
            'UPDATE utente_registrato SET stato_ban = :ban WHERE id_utente = :id',
            [
                ':ban' => $banned ? 1 : 0,
                ':id' => $userId,
            ]
        );
    }
}
