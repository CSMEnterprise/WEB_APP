<?php

require_once __DIR__ . '/BaseService.php';

class AuthService extends BaseService
{
    public function register(array $data): int
    {
        $email = strtolower(trim($data['email'] ?? ''));
        $username = trim($data['username'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $nome = trim($data['nome'] ?? '');
        $telefono = trim($data['telefono'] ?? '');
        $indirizzo = trim($data['indirizzo'] ?? '');
        $propic = trim($data['propic'] ?? '');

        $this->requireNotEmpty($email, 'email');
        $this->requireNotEmpty($username, 'username');
        $this->requireNotEmpty($password, 'password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email non valida.');
        }

        if (strlen($password) < 8) {
            throw new ServiceException('La password deve contenere almeno 8 caratteri.');
        }

        if ($this->emailExists($email)) {
            throw new ServiceException('Email già registrata.');
        }

        if ($this->usernameExists($username)) {
            throw new ServiceException('Username già utilizzato.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->execute(
            'INSERT INTO utente_registrato (email, username, password_hash, nome, telefono, indirizzo, propic)
             VALUES (:email, :username, :password_hash, :nome, :telefono, :indirizzo, :propic)',
            [
                ':email' => $email,
                ':username' => $username,
                ':password_hash' => $hash,
                ':nome' => $nome !== '' ? $nome : null,
                ':telefono' => $telefono !== '' ? $telefono : null,
                ':indirizzo' => $indirizzo !== '' ? $indirizzo : null,
                ':propic' => $propic !== '' ? $propic : null,
            ]
        );

        return $this->lastInsertId();
    }

    public function login(string $emailOrUsername, string $password): array
    {
        $login = trim($emailOrUsername);
        $this->requireNotEmpty($login, 'email o username');
        $this->requireNotEmpty($password, 'password');

        $user = $this->fetchOne(
            'SELECT * FROM utente_registrato
             WHERE email = :login OR username = :login
             LIMIT 1',
            [':login' => $login]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new ServiceException('Credenziali non valide.');
        }

        if ((int) $user['stato_ban'] === 1) {
            throw new ServiceException('Account bannato.');
        }

        unset($user['password_hash']);
        return $user;
    }

    public function adminLogin(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $this->requireNotEmpty($email, 'email');
        $this->requireNotEmpty($password, 'password');

        $admin = $this->fetchOne(
            'SELECT * FROM admin WHERE email = :email LIMIT 1',
            [':email' => $email]
        );

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            throw new ServiceException('Credenziali admin non valide.');
        }

        unset($admin['password_hash']);
        return $admin;
    }

    public function emailExists(string $email): bool
    {
        return $this->fetchOne(
            'SELECT id_utente FROM utente_registrato WHERE email = :email LIMIT 1',
            [':email' => strtolower(trim($email))]
        ) !== null;
    }

    public function usernameExists(string $username): bool
    {
        return $this->fetchOne(
            'SELECT id_utente FROM utente_registrato WHERE username = :username LIMIT 1',
            [':username' => trim($username)]
        ) !== null;
    }
}
