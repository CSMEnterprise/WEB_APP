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

        // Controlla prima la tabella admin
        $stmt = $this->db->prepare("
            SELECT *
            FROM admin
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            if (!empty($admin['stato_ban'])) {
                throw new ServiceException('Account admin bloccato.');
            }

            $admin['_is_admin'] = true;
            return $admin;
        }

        // Altrimenti cerca tra gli utenti normali
        $stmt = $this->db->prepare("
            SELECT u.*,
                   ab.id_acc_business,
                   ab.nome_azienda
            FROM utente_registrato u
            LEFT JOIN account_business ab ON ab.id_utente = u.id_utente
            WHERE u.email = ?
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

        // Segna se l'utente ha un account business associato
        $utente['_is_business'] = !empty($utente['id_acc_business']);

        return $utente;
    }

    public function register(array $data): int
    {
        $isBusinessRegistration = !empty($data['_business_registration']);

        $username = $this->clean($data['username'] ?? '');
        $email = $this->clean($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');
        $nome = $this->clean($data['nome'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');

        if ($isBusinessRegistration) {
            // Per il business username e email vengono generati/copiati dal controller
            if ($username === '' || $email === '' || $password === '') {
                throw new ServiceException('Dati di accesso mancanti. Riprova.');
            }
        } else {
            if ($username === '' || $email === '' || $password === '' || $telefono === '') {
                throw new ServiceException('Username, email, password e telefono sono obbligatori.');
            }
        }

        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
            throw new ServiceException('Lo username deve contenere 3-30 caratteri e può usare lettere, numeri, punto, trattino e underscore.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email non valida.');
        }

        if (!preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e può iniziare con +.');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException('La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        if ($password !== $passwordConfirm) {
            throw new ServiceException('Le password non coincidono.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO utente_registrato
            (email, username, password_hash, nome, telefono)
            VALUES (?, ?, ?, ?, ?)
        ");

        try {
            $stmt->execute([
                $email,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $nome !== '' ? $nome : null,
                $telefono !== '' ? $telefono : null,
            ]);
        } catch (PDOException $e) {
            throw new ServiceException('Email o username già utilizzati.');
        }

        return $this->lastInsertId();
    }
}
