<?php

require_once __DIR__ . '/BaseService.php';

class AuthService extends BaseService
{
    public function login(string $email, string $password): array
    {
        $email = $this->clean($email);

        if ($email === '' || $password === '') {
            throw new ServiceException('Email e password sono obbligatorie.');
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM utente_registrato
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);

        $utente = $stmt->fetch();

        if (!$utente || !password_verify($password, $utente['password_hash'])) {
            throw new ServiceException('Credenziali non valide.');
        }

        if (!empty($utente['stato_ban'])) {
            throw new ServiceException('Account bloccato.');
        }

        return $utente;
    }

    public function register(array $data): int
    {
        $username = $this->clean($data['username'] ?? '');
        $email = $this->clean($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $nome = $this->clean($data['nome'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');
        $indirizzo = $this->clean($data['indirizzo'] ?? '');

        if ($username === '' || $email === '' || $password === '') {
            throw new ServiceException('Username, email e password sono obbligatori.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email non valida.');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException('La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO utente_registrato
            (email, username, password_hash, nome, telefono, indirizzo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        try {
            $stmt->execute([
                $email,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $nome !== '' ? $nome : null,
                $telefono !== '' ? $telefono : null,
                $indirizzo !== '' ? $indirizzo : null
            ]);
        } catch (PDOException $e) {
            throw new ServiceException('Email o username già utilizzati.');
        }

        return $this->lastInsertId();
    }
}
