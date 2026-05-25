<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

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
            SELECT * FROM admin WHERE email = ? LIMIT 1
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

        // Cerca tra gli utenti normali
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

        // Blocca login se email non verificata
        if (isset($utente['email_verificata']) && !(bool)$utente['email_verificata']) {
            throw new ServiceException('EMAIL_NON_VERIFICATA:' . $utente['email']);
        }

        $utente['_is_business'] = !empty($utente['id_acc_business']);

        return $utente;
    }

    public function register(array $data): int
    {
        $isBusinessRegistration = !empty($data['_business_registration']);

        $username        = $this->clean($data['username'] ?? '');
        $email           = $this->clean($data['email'] ?? '');
        $password        = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');
        $nome            = $this->clean($data['nome'] ?? '');
        $telefono        = $this->clean($data['telefono'] ?? '');

        if ($isBusinessRegistration) {
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

        if (!$isBusinessRegistration && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e può iniziare con +.');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException('La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        if ($password !== $passwordConfirm) {
            throw new ServiceException('Le password non coincidono.');
        }

        // Genera token di verifica email (valido 48h)
        $token    = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+48 hours'));

        $stmt = $this->db->prepare("
            INSERT INTO utente_registrato
            (email, username, password_hash, nome, telefono, email_verificata, token_verifica, token_scadenza)
            VALUES (?, ?, ?, ?, ?, 0, ?, ?)
        ");

        try {
            $stmt->execute([
                $email,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $nome     !== '' ? $nome     : null,
                $telefono !== '' ? $telefono : null,
                $token,
                $scadenza,
            ]);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, "'email'") || str_contains($msg, 'email')) {
                throw new ServiceException('Questa email è già registrata.');
            }
            if (str_contains($msg, "'username'") || str_contains($msg, 'username')) {
                throw new ServiceException('Questo username è già in uso. Scegline un altro.');
            }
            throw new ServiceException('Registrazione non riuscita. Riprova.');
        }

        $idUtente = $this->lastInsertId();

        // Restituisce anche il token e i dati per inviare la mail
        $this->lastRegistrationToken  = $token;
        $this->lastRegistrationEmail  = $email;
        $this->lastRegistrationNome   = $nome;

        return $idUtente;
    }

    /** Dati dell'ultima registrazione, usati dal controller per inviare la mail */
    public string $lastRegistrationToken = '';
    public string $lastRegistrationEmail = '';
    public string $lastRegistrationNome  = '';

    public function verificaEmail(string $token): void
    {
        if ($token === '') {
            throw new ServiceException('Token non valido.');
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, token_scadenza
            FROM utente_registrato
            WHERE token_verifica = ? AND email_verificata = 0
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $utente = $stmt->fetch();

        if (!$utente) {
            throw new ServiceException('Token non valido o account già verificato.');
        }

        if (strtotime($utente['token_scadenza']) < time()) {
            throw new ServiceException('Il link di verifica è scaduto. Richiedi un nuovo invio.');
        }

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET email_verificata = 1, token_verifica = NULL, token_scadenza = NULL
            WHERE id_utente = ?
        ");
        $stmt->execute([$utente['id_utente']]);
    }

    public function reinviaVerifica(string $email, MailService $mail): void
    {
        $email = $this->clean($email);

        $stmt = $this->db->prepare("
            SELECT id_utente, username, nome
            FROM utente_registrato
            WHERE email = ? AND email_verificata = 0
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $utente = $stmt->fetch();

        if (!$utente) {
            // Non riveliamo se l'email esiste o meno
            return;
        }

        $token    = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+48 hours'));

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET token_verifica = ?, token_scadenza = ?
            WHERE id_utente = ?
        ");
        $stmt->execute([$token, $scadenza, $utente['id_utente']]);

        $mail->inviaVerificaEmail($email, $utente['nome'] ?? $utente['username'], $token);
    }

    public function richiestaResetPassword(string $email, MailService $mail): void
    {
        $email = $this->clean($email);

        if ($email === '') {
            throw new ServiceException('Inserisci la tua email.');
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, username, nome
            FROM utente_registrato
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $utente = $stmt->fetch();

        // Non riveliamo se l'email esiste o meno (sicurezza)
        if (!$utente) {
            return;
        }

        // Invalida eventuali token precedenti
        $this->db->prepare("
            UPDATE password_reset SET usato = 1 WHERE id_utente = ?
        ")->execute([$utente['id_utente']]);

        $token    = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->prepare("
            INSERT INTO password_reset (id_utente, token, scadenza)
            VALUES (?, ?, ?)
        ")->execute([$utente['id_utente'], $token, $scadenza]);

        $mail->inviaResetPassword($email, $utente['nome'] ?? $utente['username'], $token);
    }

    public function resetPassword(string $token, string $password, string $confirm): void
    {
        if ($token === '') {
            throw new ServiceException('Token non valido.');
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, scadenza
            FROM password_reset
            WHERE token = ? AND usato = 0
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            throw new ServiceException('Il link non è valido o è già stato utilizzato.');
        }

        if (strtotime($reset['scadenza']) < time()) {
            throw new ServiceException('Il link è scaduto. Richiedi un nuovo reset.');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException('La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        if ($password !== $confirm) {
            throw new ServiceException('Le password non coincidono.');
        }

        $this->db->prepare("
            UPDATE utente_registrato
            SET password_hash = ?
            WHERE id_utente = ?
        ")->execute([password_hash($password, PASSWORD_DEFAULT), $reset['id_utente']]);

        $this->db->prepare("
            UPDATE password_reset SET usato = 1 WHERE token = ?
        ")->execute([$token]);
    }

    public function cambiaPassword(int $idUtente, string $passwordAttuale, string $nuovaPassword, string $conferma): void
    {
        if ($passwordAttuale === '' || $nuovaPassword === '' || $conferma === '') {
            throw new ServiceException('Compila tutti i campi.');
        }

        $stmt = $this->db->prepare("SELECT password_hash FROM utente_registrato WHERE id_utente = ? LIMIT 1");
        $stmt->execute([$idUtente]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($passwordAttuale, $row['password_hash'])) {
            throw new ServiceException('La password attuale non è corretta.');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $nuovaPassword)) {
            throw new ServiceException('La nuova password deve avere almeno 10 caratteri, una maiuscola e un carattere speciale.');
        }

        if ($nuovaPassword !== $conferma) {
            throw new ServiceException('Le nuove password non coincidono.');
        }

        $stmt = $this->db->prepare("UPDATE utente_registrato SET password_hash = ? WHERE id_utente = ?");
        $stmt->execute([password_hash($nuovaPassword, PASSWORD_DEFAULT), $idUtente]);
    }

    public function getResetTokenUserId(string $token): int
    {
        $stmt = $this->db->prepare("
            SELECT id_utente FROM password_reset
            WHERE token = ? AND usato = 0 AND scadenza > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id_utente'] : 0;
    }
}
