<?php

namespace App\Controllers;

use App\Entity\EAccountBusiness;
use App\Entity\EIndirizzo;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\MailService;
use App\Services\ServiceException;
use Exception;
use finfo;
use PDO;
use PDOException;
use Throwable;

class UtenteController extends BaseController
{
    private PDO $db;
    private string $lastRegistrationToken = '';
    private string $lastRegistrationEmail = '';
    private string $lastRegistrationNome = '';

    public function __construct(PDO $db)
    {
        $this->db = $db;
        FDataBase::init($db);
    }

    public function showLogin(): void
    {
        $this->renderLogin();
    }

    public function login(array $data): void
    {
        try {
            $utente = $this->loginUser($data['email'] ?? '', $data['password'] ?? '');

            if (!empty($utente['_is_admin'])) {
                $_SESSION['user_id']  = (int) $utente['id_admin'];
                $_SESSION['username'] = 'Admin';
                $_SESSION['is_admin'] = true;
                $_SESSION['propic']   = null;
                $_SESSION['livello_sicurezza'] = (int) ($utente['livello_sicurezza'] ?? 1);
                header('Location: index.php?route=admin');
            } else {
                $_SESSION['user_id']     = (int) $utente['id_utente'];
                $_SESSION['username']    = $utente['username'];
                $_SESSION['propic']      = $utente['propic'] ?? null;
                $_SESSION['is_business'] = !empty($utente['_is_business']);
                header('Location: index.php?route=profilo');
            }
            exit;
        } catch (Exception $e) {
            $this->renderLogin($e->getMessage());
        }
    }

    public function showRegister(): void
    {
        $this->view('auth/register.tpl', [], 'Scegli registrazione');
    }

    public function showRegisterUser(): void
    {
        $this->view('utenti/registrazione_utente.tpl', [], 'Registrazione utente');
    }

    public function register(array $data): void
    {
        try {
            $this->registerUser($data);

            // Invia email di verifica
            try {
                $mail = new MailService();
                $mail->inviaVerificaEmail(
                    $this->lastRegistrationEmail,
                    $this->lastRegistrationNome,
                    $this->lastRegistrationToken
                );
            } catch (Exception $mailEx) {
                // Ignora errori mail: l'utente potrà richiedere il reinvio
            }

            header('Location: index.php?route=verifica-email-attesa&email=' . urlencode($this->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $this->view('utenti/registrazione_utente.tpl', compact('errore'), 'Registrazione utente');
        }
    }


    public function showRegisterBusiness(): void
    {
        $this->view('utenti/registrazione_business.tpl', [], 'Registrazione business');
    }

    public function registerBusiness(array $data): void
    {
        try {
            $this->db->beginTransaction();

            // Usa email aziendale come email di accesso
            $data['email'] = $data['email_aziendale'] ?? '';

            // Genera uno username univoco dal nome azienda
            $base = preg_replace('/[^A-Za-z0-9]/', '_', $data['nome_azienda'] ?? 'business');
            $base = strtolower(trim($base, '_'));
            $base = preg_replace('/_+/', '_', $base);
            $base = substr($base, 0, 26);
            if (strlen($base) < 3) {
                $base = 'biz_' . $base;
            }
            $data['username'] = $base . '_' . substr(bin2hex(random_bytes(2)), 0, 4);

            $data['_business_registration'] = true;
            $idUtente = $this->registerUser($data);
            $this->createBusinessAccount($data, $idUtente);

            $this->db->commit();

            // Invia email di verifica
            try {
                $mail = new MailService();
                $mail->inviaVerificaEmail(
                    $this->lastRegistrationEmail,
                    $this->lastRegistrationNome,
                    $this->lastRegistrationToken
                );
            } catch (Exception $mailEx) {
                // Ignora errori mail
            }

            header('Location: index.php?route=verifica-email-attesa&email=' . urlencode($this->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $errore = $e->getMessage();
            $this->view('utenti/registrazione_business.tpl', compact('errore'), 'Registrazione business');
        }
    }

    public function profilo(int $idUtente, string $filtroAnnunci = 'attivo'): void
    {
        $this->renderProfilo($this->loadProfiloData($idUtente, $filtroAnnunci));
    }

    public function venditore(int $idVenditore): void
    {
        if ($idVenditore <= 0) {
            $this->renderError('Venditore non trovato.', 404);
            return;
        }

        $venditoreEntity = FPersistentManager::utenteById($idVenditore);
        $venditore = $this->entityToArray($venditoreEntity);

        if (!$venditoreEntity || $venditoreEntity->isBannato()) {
            $this->renderError('Venditore non trovato.', 404);
            return;
        }

        $annunciVenditore = $this->entitiesToArrays(FPersistentManager::annunciByUserIdAndStato($idVenditore, 'attivo'));
        $feedbackVenditore = $this->entitiesToArrays(FPersistentManager::feedbackByVenditore($idVenditore));
        $mediaVenditore = FPersistentManager::mediaFeedbackVenditore($idVenditore);

        $this->view('utenti/venditore.tpl', compact('venditore', 'annunciVenditore', 'feedbackVenditore', 'mediaVenditore'), 'Profilo venditore');
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php?route=home');
        exit;
    }

    public function aggiornaFotoProfilo(array $files, int $idUtente): void
    {
        try {
            $url = $this->updatePropic($idUtente, $files['propic'] ?? []);
            $_SESSION['propic'] = $url;
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $this->renderProfilo($data);
        }
    }

    public function aggiornaProfiloUtente(array $data, int $idUtente): void
    {
        try {
            $this->updateUserProfile($idUtente, $data);
            header('Location: index.php?route=profilo&profilo_aggiornato=1');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['openProfiloEdit'] = true;
            $this->renderProfilo($data);
        }
    }

    public function cambiaPassword(array $data, int $idUtente): void
    {
        try {
            $this->changePassword(
                $idUtente,
                $data['password_attuale']  ?? '',
                $data['nuova_password']    ?? '',
                $data['password_confirm']  ?? ''
            );
            header('Location: index.php?route=profilo&password_aggiornata=1');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['openPasswordEdit'] = true;
            $this->renderProfilo($data);
        }
    }

    public function salvaIndirizzoSpedizione(array $data, int $idUtente): void
    {
        try {
            $this->createShippingAddress($idUtente, $data);

            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $this->renderProfilo($data);
        }
    }

    public function showModificaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        $editingIndirizzoEntity = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);
        $editingIndirizzo = $this->entityToArray($editingIndirizzoEntity);

        if (!$editingIndirizzoEntity) {
            header('Location: index.php?route=profilo');
            exit;
        }

        $data = $this->loadProfiloData($idUtente);
        $data['editingIndirizzo'] = $editingIndirizzo;
        $this->renderProfilo($data);
    }

    public function aggiornaIndirizzo(array $data, int $idUtente): void
    {
        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        try {
            $this->updateAddress($idIndirizzo, $idUtente, $data);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['editingIndirizzo'] = $this->entityToArray(FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente));
            $this->renderProfilo($data);
        }
    }

    public function eliminaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->deleteAddress($idIndirizzo, $idUtente);
        } catch (Exception $e) {
            // Ignora errori silenziosi, torna al profilo comunque
        }
        header('Location: index.php?route=profilo');
        exit;
    }

    public function impostaIndirizzoPredefinito(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->setDefaultAddress($idUtente, $idIndirizzo);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $this->renderProfilo($data);
        }
    }

    // ----------------------------------------------------------------
    // Verifica email
    // ----------------------------------------------------------------

    public function verificaEmailAttesa(): void
    {
        $email = $_GET['email'] ?? '';
        $debugLink = $this->consumeDebugMailLink('verifica');
        $this->view('utenti/verifica_email_attesa.tpl', compact('email', 'debugLink'), 'Verifica email');
    }

    public function verificaEmail(string $token): void
    {
        $successo = '';
        $errore = '';

        try {
            $this->verifyEmail($token);
            $successo = 'Email verificata con successo! Ora puoi accedere.';
        } catch (Exception $e) {
            $errore = $e->getMessage();
        }
        $this->view('utenti/verifica_email.tpl', compact('successo', 'errore'), 'Verifica email');
    }

    public function reinviaVerifica(array $data): void
    {
        $successo = '';
        $errore = '';

        try {
            $mail = new MailService();
            $this->resendVerification($data['email'] ?? '', $mail);
            $successo = 'Se l\'email è registrata e non ancora verificata, riceverai un nuovo link.';
        } catch (Exception $e) {
            $errore = $e->getMessage();
        }
        $email = $data['email'] ?? '';
        $debugLink = $this->consumeDebugMailLink('verifica');
        $this->view('utenti/verifica_email_attesa.tpl', compact('email', 'successo', 'errore', 'debugLink'), 'Verifica email');
    }

    // ----------------------------------------------------------------
    // Recupero password
    // ----------------------------------------------------------------

    public function showRecuperoPassword(): void
    {
        $this->view('utenti/recupero_password.tpl', [], 'Recupero password');
    }

    public function inviaResetPassword(array $data): void
    {
        try {
            $mail = new MailService();
            $this->requestPasswordReset($data['email'] ?? '', $mail);
        } catch (Exception $e) {
            // Silenziamo l'errore per non rivelare se l'email esiste
        }
        // Mostriamo sempre lo stesso messaggio di conferma
        $successo = 'Se l\'indirizzo è associato a un account, riceverai un\'email con le istruzioni.';
        $debugLink = $this->consumeDebugMailLink('reset');
        $this->view('utenti/recupero_password.tpl', compact('successo', 'debugLink'), 'Recupero password');
    }

    public function showResetPassword(string $token): void
    {
        $errore = '';
        $idUtente = $this->getResetTokenUserId($token);
        if ($idUtente === 0) {
            $errore = 'Il link non è valido o è scaduto.';
        }
        $this->view('utenti/reset_password.tpl', compact('token', 'idUtente', 'errore'), 'Reset password');
    }

    public function resetPassword(array $data): void
    {
        $token = $data['token'] ?? '';
        try {
            $this->resetUserPassword($token, $data['password'] ?? '', $data['password_confirm'] ?? '');
            header('Location: index.php?route=login&reset=ok');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $idUtente = $this->getResetTokenUserId($token);
            $this->view('utenti/reset_password.tpl', compact('token', 'idUtente', 'errore'), 'Reset password');
        }
    }

    private function loginUser(string $email, string $password): array
    {
        $email = $this->clean($email);

        if ($email === '' || $password === '') {
            throw new ServiceException('Email e password sono obbligatorie.');
        }

        $stmt = $this->db->prepare('SELECT * FROM admin WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            if (!empty($admin['stato_ban'])) {
                throw new ServiceException('Account admin bloccato.');
            }

            $admin['_is_admin'] = true;
            return $admin;
        }

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

        if (isset($utente['email_verificata']) && !(bool) $utente['email_verificata']) {
            throw new ServiceException('EMAIL_NON_VERIFICATA:' . $utente['email']);
        }

        $utente['_is_business'] = !empty($utente['id_acc_business']);

        return $utente;
    }

    private function registerUser(array $data): int
    {
        $isBusinessRegistration = !empty($data['_business_registration']);
        $username = $this->clean($data['username'] ?? '');
        $email = $this->clean($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');
        $nome = $this->clean($data['nome'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');

        if ($isBusinessRegistration) {
            if ($username === '' || $email === '' || $password === '') {
                throw new ServiceException('Dati di accesso mancanti. Riprova.');
            }
        } elseif ($username === '' || $email === '' || $password === '' || $telefono === '') {
            throw new ServiceException('Username, email, password e telefono sono obbligatori.');
        }

        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
            throw new ServiceException('Lo username deve contenere 3-30 caratteri e puo usare lettere, numeri, punto, trattino e underscore.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email non valida.');
        }

        if (!$isBusinessRegistration && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e puo iniziare con +.');
        }

        $this->validatePasswordPair($password, $passwordConfirm);

        $token = bin2hex(random_bytes(32));
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
                $nome !== '' ? $nome : null,
                $telefono !== '' ? $telefono : null,
                $token,
                $scadenza,
            ]);
        } catch (PDOException $e) {
            $msg = $e->getMessage();

            if (str_contains($msg, "'email'") || str_contains($msg, 'email')) {
                throw new ServiceException('Questa email e gia registrata.');
            }

            if (str_contains($msg, "'username'") || str_contains($msg, 'username')) {
                throw new ServiceException('Questo username e gia in uso. Scegline un altro.');
            }

            throw new ServiceException('Registrazione non riuscita. Riprova.');
        }

        $this->lastRegistrationToken = $token;
        $this->lastRegistrationEmail = $email;
        $this->lastRegistrationNome = $nome;

        return (int) $this->db->lastInsertId();
    }

    private function createBusinessAccount(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nomeAzienda = $this->clean($data['nome_azienda'] ?? '');
        $pIva = $this->clean($data['p_iva'] ?? $data['partita_iva'] ?? '');
        $emailAziendale = $this->clean($data['email_aziendale'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');
        $via = $this->clean($data['via'] ?? '');
        $numero = $this->clean($data['numero'] ?? '');
        $cap = $this->clean($data['cap'] ?? '');
        $citta = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese = $this->clean($data['paese'] ?? 'Italia');

        $this->validateBusinessRegistration($nomeAzienda, $pIva, $emailAziendale, $telefono, $cap, $provincia, $citta);

        try {
            $idBusiness = FPersistentManager::createBusiness(EAccountBusiness::fromArray([
                'id_utente' => $idUtente,
                'p_iva' => $pIva,
                'nome_azienda' => $nomeAzienda,
                'email_aziendale' => $emailAziendale,
                'telefono' => $telefono !== '' ? $telefono : null,
            ]));

            if ($via !== '' || $citta !== '') {
                if ($via === '' || $citta === '') {
                    throw new ServiceException('Per salvare la sede aziendale devi indicare almeno via e citta.');
                }

                FPersistentManager::createIndirizzoForBusiness(EIndirizzo::fromArray([
                    'id_business' => $idBusiness,
                    'via' => $via,
                    'numero' => $numero !== '' ? $numero : null,
                    'cap' => $cap !== '' ? $cap : null,
                    'citta' => $citta,
                    'provincia' => $provincia !== '' ? $provincia : null,
                    'paese' => $paese,
                    'predefinito' => 1,
                ]));
            }

            return $idBusiness;
        } catch (PDOException $e) {
            throw new ServiceException('Account business gia esistente o dati gia utilizzati.');
        }
    }

    private function verifyEmail(string $token): void
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
            throw new ServiceException('Token non valido o account gia verificato.');
        }

        if (strtotime($utente['token_scadenza']) < time()) {
            throw new ServiceException('Il link di verifica e scaduto. Richiedi un nuovo invio.');
        }

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET email_verificata = 1, token_verifica = NULL, token_scadenza = NULL
            WHERE id_utente = ?
        ");
        $stmt->execute([$utente['id_utente']]);
    }

    private function resendVerification(string $email, MailService $mail): void
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
            return;
        }

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+48 hours'));
        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET token_verifica = ?, token_scadenza = ?
            WHERE id_utente = ?
        ");
        $stmt->execute([$token, $scadenza, $utente['id_utente']]);

        $mail->inviaVerificaEmail($email, $utente['nome'] ?? $utente['username'], $token);
    }

    private function requestPasswordReset(string $email, MailService $mail): void
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

        if (!$utente) {
            return;
        }

        $this->db->prepare('UPDATE password_reset SET usato = 1 WHERE id_utente = ?')
            ->execute([$utente['id_utente']]);

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->db->prepare('INSERT INTO password_reset (id_utente, token, scadenza) VALUES (?, ?, ?)')
            ->execute([$utente['id_utente'], $token, $scadenza]);

        $mail->inviaResetPassword($email, $utente['nome'] ?? $utente['username'], $token);
    }

    private function resetUserPassword(string $token, string $password, string $confirm): void
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
            throw new ServiceException('Il link non e valido o e gia stato utilizzato.');
        }

        if (strtotime($reset['scadenza']) < time()) {
            throw new ServiceException('Il link e scaduto. Richiedi un nuovo reset.');
        }

        $this->validatePasswordPair($password, $confirm);

        $this->db->prepare('UPDATE utente_registrato SET password_hash = ? WHERE id_utente = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $reset['id_utente']]);

        $this->db->prepare('UPDATE password_reset SET usato = 1 WHERE token = ?')
            ->execute([$token]);
    }

    private function changePassword(int $idUtente, string $passwordAttuale, string $nuovaPassword, string $conferma): void
    {
        if ($passwordAttuale === '' || $nuovaPassword === '' || $conferma === '') {
            throw new ServiceException('Compila tutti i campi.');
        }

        $stmt = $this->db->prepare('SELECT password_hash FROM utente_registrato WHERE id_utente = ? LIMIT 1');
        $stmt->execute([$idUtente]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($passwordAttuale, $row['password_hash'])) {
            throw new ServiceException('La password attuale non e corretta.');
        }

        $this->validatePasswordPair($nuovaPassword, $conferma, 'La nuova password deve avere almeno 10 caratteri, una maiuscola e un carattere speciale.');

        $stmt = $this->db->prepare('UPDATE utente_registrato SET password_hash = ? WHERE id_utente = ?');
        $stmt->execute([password_hash($nuovaPassword, PASSWORD_DEFAULT), $idUtente]);
    }

    private function getResetTokenUserId(string $token): int
    {
        $stmt = $this->db->prepare("
            SELECT id_utente
            FROM password_reset
            WHERE token = ? AND usato = 0 AND scadenza > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        return $row ? (int) $row['id_utente'] : 0;
    }

    private function validatePasswordPair(string $password, string $confirm, ?string $passwordMessage = null): void
    {
        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException($passwordMessage ?? 'La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        if ($password !== $confirm) {
            throw new ServiceException('Le password non coincidono.');
        }
    }

    private function validateBusinessRegistration(
        string $nomeAzienda,
        string $pIva,
        string $emailAziendale,
        string $telefono,
        string $cap,
        string $provincia,
        string $citta
    ): void {
        if ($nomeAzienda === '' || $pIva === '' || $emailAziendale === '') {
            throw new ServiceException('Nome azienda, partita IVA ed email aziendale sono obbligatori.');
        }

        if (!preg_match('/^[\p{L}0-9 .&\'-]{2,80}$/u', $nomeAzienda)) {
            throw new ServiceException('Il nome azienda deve contenere 2-80 caratteri validi.');
        }

        if (!preg_match('/^[0-9]{11}$/', $pIva)) {
            throw new ServiceException('La partita IVA deve contenere esattamente 11 cifre.');
        }

        if (!filter_var($emailAziendale, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email aziendale non valida.');
        }

        if ($telefono !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e puo iniziare con +.');
        }

        if ($cap !== '' && !preg_match('/^[0-9]{5}$/', $cap)) {
            throw new ServiceException('Il CAP deve contenere esattamente 5 cifre.');
        }

        if ($provincia !== '' && !preg_match('/^[A-Za-z]{2}$/', $provincia)) {
            throw new ServiceException('La provincia deve contenere 2 lettere.');
        }

        if ($citta !== '' && !preg_match('/^[\p{L} .\'-]{2,80}$/u', $citta)) {
            throw new ServiceException('La citta deve contenere 2-80 caratteri validi.');
        }
    }

    private function updatePropic(int $idUtente, array $file): string
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $maxSize = 3 * 1024 * 1024;
        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new ServiceException('Errore durante il caricamento della foto.');
        }

        if (($file['size'] ?? 0) > $maxSize) {
            throw new ServiceException('La foto deve pesare al massimo 3 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if (!isset($allowedMime[$mime])) {
            throw new ServiceException('Puoi caricare solo immagini JPG, PNG o WEBP.');
        }

        $dir = __DIR__ . '/../../public/uploads/propic/';
        $publicDir = 'uploads/propic/';

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new ServiceException('Impossibile creare la cartella per le foto profilo.');
        }

        $filename = 'user_' . $idUtente . '_' . bin2hex(random_bytes(8)) . '.' . $allowedMime[$mime];
        $dest = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new ServiceException('Impossibile salvare la foto profilo.');
        }

        $url = $publicDir . $filename;
        FPersistentManager::updatePropicUtente($idUtente, $url);

        return $url;
    }

    private function updateUserProfile(int $idUtente, array $data): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nome = $this->clean($data['nome'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');

        if ($nome === '') {
            throw new ServiceException('Il nome non puo essere vuoto.');
        }

        if ($telefono !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e puo iniziare con +.');
        }

        FPersistentManager::updateProfiloUtente($idUtente, $nome, $telefono !== '' ? $telefono : null);
    }

    private function createShippingAddress(int $idUtente, array $data): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nome = $this->clean($data['nome'] ?? '');
        $via = $this->clean($data['via'] ?? '');
        $numero = $this->clean($data['numero'] ?? '');
        $cap = $this->clean($data['cap'] ?? '');
        $citta = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese = $this->clean($data['paese'] ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e citta sono obbligatori.');
        }

        if ($nome !== '') {
            FPersistentManager::updateNomeUtente($idUtente, $nome);
        }

        FPersistentManager::createIndirizzoForUser(EIndirizzo::fromArray([
            'id_utente' => $idUtente,
            'via' => $via,
            'numero' => $numero !== '' ? $numero : null,
            'cap' => $cap !== '' ? $cap : null,
            'citta' => $citta,
            'provincia' => $provincia !== '' ? $provincia : null,
            'paese' => $paese,
            'predefinito' => FPersistentManager::countIndirizziByUser($idUtente) === 0 ? 1 : 0,
        ]));
    }

    private function updateAddress(int $idIndirizzo, int $idUtente, array $data): void
    {
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');
        $this->requirePositiveId($idUtente, 'Utente');

        if (!FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo non trovato.');
        }

        $via = $this->clean($data['via'] ?? '');
        $numero = $this->clean($data['numero'] ?? '');
        $cap = $this->clean($data['cap'] ?? '');
        $citta = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese = $this->clean($data['paese'] ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e citta sono obbligatori.');
        }

        FPersistentManager::updateIndirizzoForUser(EIndirizzo::fromArray([
            'id_indirizzo' => $idIndirizzo,
            'id_utente' => $idUtente,
            'via' => $via,
            'numero' => $numero !== '' ? $numero : null,
            'cap' => $cap !== '' ? $cap : null,
            'citta' => $citta,
            'provincia' => $provincia !== '' ? $provincia : null,
            'paese' => $paese,
        ]));
    }

    private function deleteAddress(int $idIndirizzo, int $idUtente): void
    {
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');
        $this->requirePositiveId($idUtente, 'Utente');

        $indirizzo = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);

        if (!$indirizzo) {
            throw new ServiceException('Indirizzo non trovato.');
        }

        FPersistentManager::deleteIndirizzoForUser($idIndirizzo, $idUtente);

        if ($indirizzo->isPredefinito()) {
            FPersistentManager::makeMostRecentIndirizzoDefault($idUtente);
        }
    }

    private function setDefaultAddress(int $idUtente, int $idIndirizzo): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');

        if (!FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo non valido.');
        }

        $this->db->beginTransaction();

        try {
            FPersistentManager::setIndirizzoPredefinito($idUtente, $idIndirizzo);
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new ServiceException('Impossibile impostare l indirizzo predefinito.');
        }
    }

    private function loadProfiloData(int $idUtente, string $filtroAnnunci = 'attivo'): array
    {
        $filtroAnnunci = $filtroAnnunci === 'venduto' ? 'venduto' : 'attivo';
        $isBusiness = !empty($_SESSION['is_business']);

        return [
            'utente' => $this->entityToArray(FPersistentManager::utenteById($idUtente)),
            'indirizziUtente' => $isBusiness ? [] : $this->entitiesToArrays(FPersistentManager::indirizziByUser($idUtente)),
            'filtroAnnunci' => $filtroAnnunci,
            'annunciUtente' => $this->entitiesToArrays(FPersistentManager::annunciByUserIdAndStato($idUtente, $filtroAnnunci)),
            'titoloAnnunciProfilo' => $filtroAnnunci === 'venduto' ? 'Annunci venduti' : 'Annunci attivi',
            'cronologiaPagamenti' => $isBusiness ? [] : $this->entitiesToArrays(FPersistentManager::cronologiaPagamentiByUser($idUtente)),
        ];
    }

    private function renderProfilo(array $data): void
    {
        $this->view('utenti/profilo.tpl', $data, 'Profilo');
    }

    private function consumeDebugMailLink(string $tipo): string
    {
        $debugMail = $_SESSION['debug_mail'] ?? null;

        if (!is_array($debugMail) || ($debugMail['tipo'] ?? '') !== $tipo) {
            return '';
        }

        $link = (string) ($debugMail['link'] ?? '');
        unset($_SESSION['debug_mail']);

        return $link;
    }

    private function renderLogin(string $errore = ''): void
    {
        $isEmailNonVerificata = str_starts_with($errore, 'EMAIL_NON_VERIFICATA:');
        $emailNonVerificata = $isEmailNonVerificata
            ? substr($errore, strlen('EMAIL_NON_VERIFICATA:'))
            : '';

        $this->view('auth/login.tpl', [
            'resetOk' => ($_GET['reset'] ?? '') === 'ok',
            'errore' => $isEmailNonVerificata ? 'Email non verificata.' : $errore,
            'isEmailNonVerificata' => $isEmailNonVerificata,
            'emailNonVerificataUrl' => urlencode($emailNonVerificata),
        ], 'Login');
    }
}
