<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\SessionManager;
use App\Entity\{
    EAccountBusiness,
    EIndirizzo
};
use App\Foundation\FPersistentManager;
use App\Services\{
    MailService,
    ServiceException
};
use Exception;
use finfo;
use PDOException;

/**
 * Gestisce autenticazione, registrazione, profilo utente, indirizzi e recupero password.
 */
class UtenteController extends BaseController
{
    private string $lastRegistrationToken = '';
    private string $lastRegistrationEmail = '';
    private string $lastRegistrationNome = '';
    private const PHONE_COUNTRY_RULES = [
        'IT' => ['label' => 'Italia', 'prefix' => '+39', 'min' => 6, 'max' => 10],
        'FR' => ['label' => 'Francia', 'prefix' => '+33', 'min' => 9, 'max' => 9],
        'DE' => ['label' => 'Germania', 'prefix' => '+49', 'min' => 5, 'max' => 11],
        'ES' => ['label' => 'Spagna', 'prefix' => '+34', 'min' => 9, 'max' => 9],
        'GB' => ['label' => 'Regno Unito', 'prefix' => '+44', 'min' => 7, 'max' => 10],
        'US' => ['label' => 'Stati Uniti', 'prefix' => '+1', 'min' => 10, 'max' => 10],
        'CA' => ['label' => 'Canada', 'prefix' => '+1', 'min' => 10, 'max' => 10],
        'CH' => ['label' => 'Svizzera', 'prefix' => '+41', 'min' => 9, 'max' => 9],
        'AT' => ['label' => 'Austria', 'prefix' => '+43', 'min' => 4, 'max' => 13],
        'BE' => ['label' => 'Belgio', 'prefix' => '+32', 'min' => 8, 'max' => 9],
        'NL' => ['label' => 'Paesi Bassi', 'prefix' => '+31', 'min' => 9, 'max' => 9],
        'PT' => ['label' => 'Portogallo', 'prefix' => '+351', 'min' => 9, 'max' => 9],
        'PL' => ['label' => 'Polonia', 'prefix' => '+48', 'min' => 9, 'max' => 9],
        'RO' => ['label' => 'Romania', 'prefix' => '+40', 'min' => 9, 'max' => 9],
        'AL' => ['label' => 'Albania', 'prefix' => '+355', 'min' => 8, 'max' => 9],
        'BR' => ['label' => 'Brasile', 'prefix' => '+55', 'min' => 10, 'max' => 11],
    ];

    public function loginFormOrSubmit(array $data = []): void
    {
        if ((Request::server('REQUEST_METHOD', 'GET')) === 'POST') {
            $this->login($data);
            return;
        }

        $this->showLogin();
    }

    /**
     * Mostra la pagina di accesso.
     */
    public function showLogin(): void
    {
        $this->renderLogin();
    }

    /**
     * Autentica admin o utenti normali e prepara le variabili di sessione.
     */
    public function login(array $data): void
    {
        try {
            $utente = $this->loginUser($data['email'] ?? '', $data['password'] ?? '');
            SessionManager::regenerateForAuthentication();

            if (!empty($utente['_is_admin'])) {
                SessionManager::set('user_id', (int) $utente['id_admin']);
                SessionManager::set('username', 'Admin');
                SessionManager::set('is_admin', true);
                SessionManager::set('is_business', false);
                SessionManager::set('business_id', 0);
                SessionManager::set('propic', null);
                SessionManager::set('livello_sicurezza', (int) ($utente['livello_sicurezza'] ?? 1));
                header('Location: /admin/index');
            } else {
                SessionManager::set('user_id', (int) $utente['id_utente']);
                SessionManager::set('username', $utente['username']);
                SessionManager::set('propic', $utente['propic'] ?? null);
                SessionManager::set('is_admin', false);
                SessionManager::set('is_business', !empty($utente['_is_business']));
                SessionManager::set('business_id', (int) ($utente['id_acc_business'] ?? 0));
                header('Location: /utente/profilo');
            }
            exit;
        } catch (Exception $e) {
            $this->renderLogin($e->getMessage());
        }
    }

    /**
     * Schermata intermedia per scegliere tra registrazione utente e business.
     */
    public function showRegister(): void
    {
        $this->view('auth/register.tpl', [], 'Scegli registrazione');
    }

    /**
     * Mostra il form di registrazione per utenti acquirenti/venditori non business.
     */
    public function showRegisterUser(): void
    {
        $this->view('utenti/registrazione_utente.tpl', [], 'Registrazione utente');
    }

    public function registerUserFormOrSubmit(array $data = []): void
    {
        if ((Request::server('REQUEST_METHOD', 'GET')) === 'POST') {
            $this->register($data);
            return;
        }

        $this->showRegisterUser();
    }

    /**
     * Registra un utente normale e avvia il flusso di verifica email.
     */
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
                SessionManager::set('verification_mail_error', 'Account creato, ma invio email non riuscito. Controlla la configurazione Mailtrap e usa il reinvio.');
            }

            header('Location: /auth/verifica-email-attesa?email=' . urlencode($this->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $this->view('utenti/registrazione_utente.tpl', compact('errore'), 'Registrazione utente');
        }
    }


    /**
     * Mostra il form di registrazione business.
     */
    public function showRegisterBusiness(): void
    {
        $this->view('utenti/registrazione_business.tpl', [], 'Registrazione business');
    }

    public function registerBusinessFormOrSubmit(array $data = []): void
    {
        if ((Request::server('REQUEST_METHOD', 'GET')) === 'POST') {
            $this->registerBusiness($data);
            return;
        }

        $this->showRegisterBusiness();
    }

    /**
     * Registra utente base e account business nella stessa transazione.
     */
    public function registerBusiness(array $data): void
    {
        try {
            FPersistentManager::transaction(function () use (&$data): void {
                // Usa email aziendale come email di accesso
                $data['email'] = $data['email_aziendale'] ?? '';

                // Genera uno username univoco dal nome azienda
                $base = preg_replace('/[^A-Za-z0-9]/', '', $data['nome_azienda'] ?? 'business');
                $base = strtolower(trim($base, '_'));
                $base = substr($base, 0, 24);
                if (strlen($base) < 3 || !preg_match('/[A-Za-z]/', $base)) {
                    $base = 'business';
                }
                $data['username'] = $base . substr(bin2hex(random_bytes(3)), 0, 6);

                $data['_business_registration'] = true;
                $idUtente = $this->registerUser($data);
                $this->createBusinessAccount($data, $idUtente);
            });

            // Invia email di verifica
            try {
                $mail = new MailService();
                $mail->inviaVerificaEmail(
                    $this->lastRegistrationEmail,
                    $this->lastRegistrationNome,
                    $this->lastRegistrationToken
                );
            } catch (Exception $mailEx) {
                SessionManager::set('verification_mail_error', 'Account creato, ma invio email non riuscito. Controlla la configurazione Mailtrap e usa il reinvio.');
            }

            header('Location: /auth/verifica-email-attesa?email=' . urlencode($this->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $this->view('utenti/registrazione_business.tpl', compact('errore'), 'Registrazione business');
        }
    }

    /**
     * Carica tutti i dati necessari alla pagina profilo.
     */
    public function profilo(int $idUtente, string $filtroAnnunci = 'attivo'): void
    {
        $this->renderProfilo($this->loadProfiloData($idUtente, $filtroAnnunci));
    }

    public function profiloCorrente(int $idUtente, string $filtroAnnunci = 'attivo'): void
    {
        if (SessionManager::has('is_admin')) {
            (new AdminController())->dashboard($idUtente);
            return;
        }

        $this->profilo($idUtente, $filtroAnnunci);
    }

    /**
     * Mostra il profilo pubblico di un venditore non bannato.
     */
    public function venditore(int $idVenditore): void
    {
        if ($idVenditore <= 0) {
            $this->renderError('Venditore non trovato.', 404);
            return;
        }

        $venditoreEntity = FPersistentManager::utenteById($idVenditore);
        $venditore = $venditoreEntity;

        if (!$venditoreEntity || $venditoreEntity->isBannato()) {
            $this->renderError('Venditore non trovato.', 404);
            return;
        }

        $businessEntity = FPersistentManager::businessByUser($idVenditore);
        if ($businessEntity) {
            $business = $businessEntity;
            $annunci = FPersistentManager::annunciByBusinessIdAndStato((int) $businessEntity->getIdAccBusiness(), 'attivo');
            $isPublicVetrina = true;

            $this->view('business/profilo.tpl', compact('business', 'annunci', 'isPublicVetrina'), 'Vetrina ' . ($businessEntity->getNomeAzienda() ?: 'PRO'));
            return;
        }

        $annunciVenditore = FPersistentManager::annunciByUserIdAndStato($idVenditore, 'attivo');
        $feedbackVenditore = FPersistentManager::feedbackByVenditore($idVenditore);
        $mediaVenditore = FPersistentManager::mediaFeedbackVenditore($idVenditore);

        $this->view('utenti/venditore.tpl', compact('venditore', 'annunciVenditore', 'feedbackVenditore', 'mediaVenditore'), 'Profilo venditore');
    }

    /**
     * Termina la sessione e riporta alla home.
     */
    public function logout(): void
    {
        SessionManager::destroy();
        header('Location: /home/index');
        exit;
    }

    /**
     * Aggiorna la foto profilo e sincronizza subito la sessione.
     */
    public function aggiornaFotoProfilo(array $files, int $idUtente): void
    {
        try {
            $url = $this->updatePropic($idUtente, $files['propic'] ?? []);
            SessionManager::set('propic', $url);
            header('Location: /utente/profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $this->renderProfilo($data);
        }
    }

    /**
     * Aggiorna i dati anagrafici visibili nel profilo.
     */
    public function aggiornaProfiloUtente(array $data, int $idUtente): void
    {
        try {
            $this->updateUserProfile($idUtente, $data);
            header('Location: /utente/profilo?profilo_aggiornato=1');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['openProfiloEdit'] = true;
            $this->renderProfilo($data);
        }
    }

    /**
     * Cambia password dopo aver verificato quella attuale.
     */
    public function cambiaPassword(array $data, int $idUtente): void
    {
        try {
            $this->changePassword(
                $idUtente,
                $data['password_attuale']  ?? '',
                $data['nuova_password']    ?? '',
                $data['password_confirm']  ?? ''
            );
            header('Location: /utente/profilo?password_aggiornata=1');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['openPasswordEdit'] = true;
            $this->renderProfilo($data);
        }
    }

    /**
     * Salva un nuovo indirizzo di spedizione per utenti non business.
     */
    public function salvaIndirizzoSpedizione(array $data, int $idUtente): void
    {
        try {
            $this->createShippingAddress($idUtente, $data);

            header('Location: /utente/profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $this->renderProfilo($data);
        }
    }

    /**
     * Riapre il profilo in modalita modifica indirizzo.
     */
    public function showModificaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        $editingIndirizzoEntity = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);
        $editingIndirizzo = $editingIndirizzoEntity;

        if (!$editingIndirizzoEntity) {
            header('Location: /utente/profilo');
            exit;
        }

        $data = $this->loadProfiloData($idUtente);
        $data['editingIndirizzo'] = $editingIndirizzo;
        $this->renderProfilo($data);
    }

    /**
     * Aggiorna un indirizzo solo se appartiene all'utente corrente.
     */
    public function aggiornaIndirizzo(array $data, int $idUtente): void
    {
        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        try {
            $this->updateAddress($idIndirizzo, $idUtente, $data);
            header('Location: /utente/profilo');
            exit;
        } catch (Exception $e) {
            $data = $this->loadProfiloData($idUtente);
            $data['errore'] = $e->getMessage();
            $data['editingIndirizzo'] = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);
            $this->renderProfilo($data);
        }
    }

    /**
     * Elimina un indirizzo e ripristina un predefinito se necessario.
     */
    public function eliminaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->deleteAddress($idIndirizzo, $idUtente);
        } catch (Exception $e) {
            // Ignora errori silenziosi, torna al profilo comunque
        }
        header('Location: /utente/profilo');
        exit;
    }

    /**
     * Imposta l'indirizzo principale usato nel checkout.
     */
    public function impostaIndirizzoPredefinito(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->setDefaultAddress($idUtente, $idIndirizzo);
            header('Location: /utente/profilo');
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

    /**
     * Pagina mostrata dopo registrazione, in attesa della conferma email.
     */
    public function verificaEmailAttesa(): void
    {
        $email = Request::get('email', '');
        $errore = $this->consumeVerificationMailError();
        $debugLink = $this->consumeDebugMailLink('verifica');
        $this->view('utenti/verifica_email_attesa.tpl', compact('email', 'errore', 'debugLink'), 'Verifica email');
    }

    /**
     * Consuma il token arrivato via email e abilita l'account.
     */
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

    /**
     * Genera un nuovo token di verifica senza rivelare dettagli sensibili.
     */
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

    /**
     * Mostra il form per richiedere il reset password.
     */
    public function showRecuperoPassword(): void
    {
        $this->view('utenti/recupero_password.tpl', [], 'Recupero password');
    }

    public function passwordRecoveryFormOrSubmit(array $data = []): void
    {
        if ((Request::server('REQUEST_METHOD', 'GET')) === 'POST') {
            $this->inviaResetPassword($data);
            return;
        }

        $this->showRecuperoPassword();
    }

    /**
     * Invia email di reset mostrando sempre una risposta generica.
     */
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

    /**
     * Mostra il form di inserimento nuova password se il token e valido.
     */
    public function showResetPassword(string $token): void
    {
        $errore = '';
        $idUtente = $this->getResetTokenUserId($token);
        if ($idUtente === 0) {
            $errore = 'Il link non è valido o è scaduto.';
        }
        $this->view('utenti/reset_password.tpl', compact('token', 'idUtente', 'errore'), 'Reset password');
    }

    /**
     * Applica il cambio password richiesto tramite token monouso.
     */
    public function resetPassword(array $data): void
    {
        $token = $data['token'] ?? '';
        try {
            $this->resetUserPassword($token, $data['password'] ?? '', $data['password_confirm'] ?? '');
            header('Location: /auth/login?reset=ok');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $idUtente = $this->getResetTokenUserId($token);
            $this->view('utenti/reset_password.tpl', compact('token', 'idUtente', 'errore'), 'Reset password');
        }
    }

    public function passwordResetFormOrSubmit(array $data = [], string $token = ''): void
    {
        if ((Request::server('REQUEST_METHOD', 'GET')) === 'POST') {
            $this->resetPassword($data);
            return;
        }

        $this->showResetPassword($token);
    }

    /**
     * Verifica credenziali contro admin e utenti registrati.
     */
    private function loginUser(string $email, string $password): array
    {
        $email = $this->clean($email);

        if ($email === '' || $password === '') {
            throw new ServiceException('Email e password sono obbligatorie.');
        }

        // Gli admin vivono in una tabella separata, quindi vengono controllati prima.
        $admin = FPersistentManager::adminByEmailForLogin($email);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            if (!empty($admin['stato_ban'])) {
                throw new ServiceException('Account admin bloccato.');
            }

            $admin['_is_admin'] = true;
            return $admin;
        }

        $utenteEntity = FPersistentManager::utenteByEmailForLogin($email);
        $utente = $utenteEntity ? $utenteEntity->toArray() : null;

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

    /**
     * Valida e inserisce un nuovo record utente con token verifica email.
     */
    private function registerUser(array $data): int
    {
        // Questo metodo e riusato anche dalla registrazione business con vincoli leggermente diversi.
        $isBusinessRegistration = !empty($data['_business_registration']);
        $username = $this->clean($data['username'] ?? '');
        $email = $this->clean($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');
        $nome = $this->clean($data['nome'] ?? '');
        $telefono = $this->normalizeRegistrationPhone($data, !$isBusinessRegistration);

        if ($isBusinessRegistration) {
            if ($username === '' || $email === '' || $password === '') {
                throw new ServiceException('Dati di accesso mancanti. Riprova.');
            }
        } elseif ($username === '' || $email === '' || $password === '' || $telefono === '') {
            throw new ServiceException('Username, email, password e telefono sono obbligatori.');
        }

        $this->validateUsername($username);

        if ($nome !== '') {
            $this->validatePersonName($nome);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email non valida.');
        }

        $this->validatePasswordPair($password, $passwordConfirm);

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+48 hours'));
        try {
            // Purge dei tentativi mai verificati + insert in un'unica transazione:
            // se l'insert fallisce non resta nessuna riga sporca nel DB, e i dati
            // di una registrazione precedente non confermata possono essere riusati.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $idUtente = FPersistentManager::transaction(static function () use (
                $email,
                $username,
                $passwordHash,
                $nome,
                $telefono,
                $token,
                $scadenza
            ): int {
                FPersistentManager::purgeUnverifiedRegistration($email, $username);

                return FPersistentManager::createUtenteWithVerification(
                    $email,
                    $username,
                    $passwordHash,
                    $nome !== '' ? $nome : null,
                    $telefono !== '' ? $telefono : null,
                    $token,
                    $scadenza
                );
            });
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

        return $idUtente;
    }
    /**
     * Crea i dati aziendali collegati a un utente gia registrato.
     */
    private function createBusinessAccount(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nomeAzienda = $this->clean($data['nome_azienda'] ?? '');
        $pIva = $this->clean($data['p_iva'] ?? $data['partita_iva'] ?? '');
        $emailAziendale = $this->clean($data['email_aziendale'] ?? '');
        $telefono = $this->normalizeRegistrationPhone($data, true);
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

    /**
     * Controlla e consuma il token di verifica email.
     */
    private function verifyEmail(string $token): void
    {
        // Il token conferma l'email e viene invalidato subito dopo l'uso.
        if ($token === '') {
            throw new ServiceException('Token non valido.');
        }

        $utente = FPersistentManager::utenteByVerificationToken($token);

        if (!$utente) {
            throw new ServiceException('Token non valido o account gia verificato.');
        }

        if (strtotime((string) $utente->getTokenScadenza()) < time()) {
            throw new ServiceException('Il link di verifica e scaduto. Richiedi un nuovo invio.');
        }

        FPersistentManager::confirmUtenteEmail((int) $utente->getIdUtente());
    }

    /**
     * Rigenera token verifica email per account ancora non confermati.
     */
    private function resendVerification(string $email, MailService $mail): void
    {
        $email = $this->clean($email);
        $utente = FPersistentManager::unverifiedUtenteByEmail($email);

        if (!$utente) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+48 hours'));
        FPersistentManager::updateUtenteVerificationToken((int) $utente->getIdUtente(), $token, $scadenza);

        $mail->inviaVerificaEmail($email, $utente->getNome() ?? $utente->getUsername(), $token);
    }

    /**
     * Crea un token monouso di recupero password.
     */
    private function requestPasswordReset(string $email, MailService $mail): void
    {
        $email = $this->clean($email);

        if ($email === '') {
            throw new ServiceException('Inserisci la tua email.');
        }

        $utente = FPersistentManager::utenteBasicByEmail($email);

        if (!$utente) {
            return;
        }

        $idUtente = (int) $utente->getIdUtente();
        FPersistentManager::invalidatePasswordResetsForUser($idUtente);

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', strtotime('+1 hour'));
        FPersistentManager::createPasswordReset($idUtente, $token, $scadenza);

        $mail->inviaResetPassword($email, $utente->getNome() ?? $utente->getUsername(), $token);
    }

    /**
     * Cambia password usando un token reset valido e non scaduto.
     */
    private function resetUserPassword(string $token, string $password, string $confirm): void
    {
        if ($token === '') {
            throw new ServiceException('Token non valido.');
        }

        $reset = FPersistentManager::passwordResetByToken($token);

        if (!$reset) {
            throw new ServiceException('Il link non e valido o e gia stato utilizzato.');
        }

        if (strtotime($reset->getScadenza()) < time()) {
            throw new ServiceException('Il link e scaduto. Richiedi un nuovo reset.');
        }

        $this->validatePasswordPair($password, $confirm);

        FPersistentManager::updateUtentePasswordHash($reset->getIdUtente(), password_hash($password, PASSWORD_DEFAULT));
        FPersistentManager::markPasswordResetTokenUsed($token);
    }

    /**
     * Cambio password da profilo: richiede la password attuale.
     */
    private function changePassword(int $idUtente, string $passwordAttuale, string $nuovaPassword, string $conferma): void
    {
        if ($passwordAttuale === '' || $nuovaPassword === '' || $conferma === '') {
            throw new ServiceException('Compila tutti i campi.');
        }

        $utente = FPersistentManager::utenteById($idUtente);

        if (!$utente || !password_verify($passwordAttuale, $utente->getPasswordHash())) {
            throw new ServiceException('La password attuale non e corretta.');
        }

        $this->validatePasswordPair($nuovaPassword, $conferma, 'La nuova password deve avere almeno 10 caratteri, una maiuscola e un carattere speciale.');

        FPersistentManager::updateUtentePasswordHash($idUtente, password_hash($nuovaPassword, PASSWORD_DEFAULT));
    }

    /**
     * Restituisce l'utente collegato a un token reset valido, oppure 0.
     */
    private function getResetTokenUserId(string $token): int
    {
        return FPersistentManager::userIdByValidPasswordResetToken($token);
    }

    /**
     * Valida robustezza e conferma della password.
     */
    private function validatePasswordPair(string $password, string $confirm, ?string $passwordMessage = null): void
    {
        // Regola unica per tutte le password utente: lunghezza, maiuscola e carattere speciale.
        if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
            throw new ServiceException($passwordMessage ?? 'La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale.');
        }

        if ($password !== $confirm) {
            throw new ServiceException('Le password non coincidono.');
        }
    }

    private function validateUsername(string $username): void
    {
        if (!preg_match('/^(?=.*[A-Za-z])[A-Za-z0-9]{3,30}$/', $username)) {
            throw new ServiceException('Lo username deve contenere 3-30 caratteri, solo lettere e numeri, e almeno una lettera.');
        }
    }

    private function validatePersonName(string $nome): void
    {
        if (!preg_match('/^[\p{L} ]{2,50}$/u', $nome)) {
            throw new ServiceException('Il nome completo deve contenere solo lettere e spazi, senza numeri o caratteri speciali.');
        }
    }

    /**
     * Normalizza il telefono inserito in registrazione in formato internazionale senza spazi.
     */
    private function normalizeRegistrationPhone(array $data, bool $required): string
    {
        $hasSplitPhone = array_key_exists('telefono_paese', $data) || array_key_exists('telefono_numero', $data);

        if ($hasSplitPhone) {
            $country = strtoupper($this->clean($data['telefono_paese'] ?? ''));

            if (!isset(self::PHONE_COUNTRY_RULES[$country])) {
                throw new ServiceException('Seleziona un paese valido per il telefono.');
            }

            $rule = self::PHONE_COUNTRY_RULES[$country];
            $digits = preg_replace('/\D+/', '', (string) ($data['telefono_numero'] ?? ''));

            if ($digits === '') {
                if ($required) {
                    throw new ServiceException('Il telefono e obbligatorio.');
                }

                return '';
            }

            $length = strlen($digits);
            $min = (int) $rule['min'];
            $max = (int) $rule['max'];

            if ($length < $min || $length > $max) {
                $range = $min === $max ? 'esattamente ' . $max . ' cifre' : $min . '-' . $max . ' cifre';
                throw new ServiceException('Il numero per ' . $rule['label'] . ' deve contenere ' . $range . '.');
            }

            return $rule['prefix'] . $digits;
        }

        $telefono = preg_replace('/[\s.-]+/', '', $this->clean($data['telefono'] ?? ''));

        if ($telefono === '') {
            if ($required) {
                throw new ServiceException('Il telefono e obbligatorio.');
            }

            return '';
        }

        if (!preg_match('/^\+[1-9][0-9]{7,14}$/', $telefono)) {
            throw new ServiceException('Il telefono deve includere il prefisso internazionale e restare entro 15 cifre totali.');
        }

        return $telefono;
    }

    /**
     * Valida campi specifici della registrazione business.
     */
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

        if (!preg_match('/^(?=.*\p{L})[\p{L}0-9 .\'-]{2,80}$/u', $nomeAzienda)) {
            throw new ServiceException('Il nome azienda deve contenere 2-80 caratteri, almeno una lettera e solo lettere, numeri, spazi, punto, apostrofo o trattino.');
        }

        if (!preg_match('/^[0-9]{11}$/', $pIva)) {
            throw new ServiceException('La partita IVA deve contenere esattamente 11 cifre.');
        }

        if (!filter_var($emailAziendale, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email aziendale non valida.');
        }

        if ($telefono !== '' && !preg_match('/^\+[1-9][0-9]{7,14}$/', $telefono)) {
            throw new ServiceException('Il telefono deve includere il prefisso internazionale e restare entro 15 cifre totali.');
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

    /**
     * Valida e salva una nuova immagine profilo.
     */
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
        $publicDir = '/uploads/propic/';

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

    /**
     * Aggiorna nome e telefono dell'utente.
     */
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

    /**
     * Crea un indirizzo di spedizione e lo rende predefinito se e il primo.
     */
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

    /**
     * Aggiorna un indirizzo gia esistente dell'utente.
     */
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

    /**
     * Cancella un indirizzo dell'utente.
     */
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
            // Evita che l'utente resti senza indirizzo predefinito se ne ha altri.
            FPersistentManager::makeMostRecentIndirizzoDefault($idUtente);
        }
    }

    /**
     * Rende predefinito un indirizzo dell'utente con transazione dedicata.
     */
    private function setDefaultAddress(int $idUtente, int $idIndirizzo): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');

        if (!FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo non valido.');
        }

        try {
            // L'operazione toglie il flag agli altri indirizzi e lo assegna a quello scelto.
            FPersistentManager::setIndirizzoPredefinito($idUtente, $idIndirizzo);
        } catch (Exception $e) {
            throw new ServiceException('Impossibile impostare l indirizzo predefinito.');
        }
    }

    /**
     * Aggrega dati profilo, annunci, indirizzi e pagamenti per la view.
     */
    private function loadProfiloData(int $idUtente, string $filtroAnnunci = 'attivo'): array
    {
        // I business vendono soltanto: niente indirizzi di spedizione o cronologia acquisti.
        $filtroAnnunci = $filtroAnnunci === 'venduto' ? 'venduto' : 'attivo';
        $isBusiness = SessionManager::has('is_business');
        $business = $isBusiness ? FPersistentManager::businessByUser($idUtente) : null;
        $annunci = $business
            ? FPersistentManager::annunciByBusinessIdAndStato((int) $business->getIdAccBusiness(), $filtroAnnunci)
            : FPersistentManager::annunciByUserIdAndStato($idUtente, $filtroAnnunci);

        return [
            'utente' => FPersistentManager::utenteById($idUtente),
            'indirizziUtente' => $isBusiness ? [] : FPersistentManager::indirizziByUser($idUtente),
            'filtroAnnunci' => $filtroAnnunci,
            'annunciUtente' => $annunci,
            'titoloAnnunciProfilo' => $filtroAnnunci === 'venduto' ? 'Annunci venduti' : 'Annunci attivi',
            'cronologiaPagamenti' => $isBusiness ? [] : FPersistentManager::cronologiaPagamentiByUser($idUtente),
        ];
    }

    /**
     * Render unico del profilo per evitare duplicazione tra successi ed errori.
     */
    private function renderProfilo(array $data): void
    {
        $this->view('utenti/profilo.tpl', $data, 'Profilo');
    }

    private function consumeVerificationMailError(): string
    {
        $errore = (string) SessionManager::pull('verification_mail_error', '');

        return $errore;
    }

    /**
     * Recupera un link email di debug salvato in sessione.
     */
    private function consumeDebugMailLink(string $tipo): string
    {
        // Il link debug viene consumato una sola volta per non mostrarlo in pagine successive.
        $debugMail = SessionManager::get('debug_mail');

        if (!is_array($debugMail) || ($debugMail['tipo'] ?? '') !== $tipo) {
            return '';
        }

        $link = (string) ($debugMail['link'] ?? '');
        SessionManager::remove('debug_mail');

        return $link;
    }

    /**
     * Prepara variabili speciali della pagina login.
     */
    private function renderLogin(string $errore = ''): void
    {
        // EMAIL_NON_VERIFICATA e un marker interno usato per mostrare la CTA di reinvio.
        $isEmailNonVerificata = str_starts_with($errore, 'EMAIL_NON_VERIFICATA:');
        $emailNonVerificata = $isEmailNonVerificata
            ? substr($errore, strlen('EMAIL_NON_VERIFICATA:'))
            : '';
        $sessionExpired = SessionManager::has('session_expired');
        SessionManager::remove('session_expired');

        $this->view('auth/login.tpl', [
            'resetOk' => Request::get('reset', '') === 'ok',
            'sessionExpired' => $sessionExpired,
            'errore' => $isEmailNonVerificata ? 'Email non verificata.' : $errore,
            'isEmailNonVerificata' => $isEmailNonVerificata,
            'emailNonVerificataUrl' => urlencode($emailNonVerificata),
        ], 'Login');
    }
}
