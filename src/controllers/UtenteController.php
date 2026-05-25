<?php

namespace App\Controllers;

use App\Entity\EAccountBusiness;
use App\Entity\EAnnuncio;
use App\Entity\EIndirizzo;
use App\Entity\EPagamento;
use App\Entity\EUtenteRegistrato;
use App\Foundation\SmartyView;
use App\Services\AdminService;
use App\Services\AnnuncioService;
use App\Services\AuthService;
use App\Services\BusinessService;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\FeedbackService;
use App\Services\MailService;
use App\Services\PaymentService;
use App\Services\SegnalazioneService;
use App\Services\ServiceException;
use App\Services\UserService;
use App\Services\WishlistService;
use Exception;
use PDO;

class UtenteController
{
    private AuthService $authService;
    private UserService $userService;
    private AnnuncioService $annuncioService;
    private BusinessService $businessService;
    private PaymentService $paymentService;
    private FeedbackService $feedbackService;
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->authService = new AuthService($db);
        $this->userService = new UserService($db);
        $this->annuncioService = new AnnuncioService($db);
        $this->businessService = new BusinessService($db);
        $this->paymentService = new PaymentService($db);
        $this->feedbackService = new FeedbackService($db);
    }

    public function showLogin(): void
    {
        $this->renderLogin();
    }

    public function login(array $data): void
    {
        try {
            $utente = $this->authService->login($data['email'] ?? '', $data['password'] ?? '');

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
        require __DIR__ . '/../views/utenti/registrazione.php';
    }

    public function showRegisterUser(): void
    {
        require __DIR__ . '/../views/utenti/registrazione_utente.php';
    }

    public function register(array $data): void
    {
        try {
            $this->authService->register($data);

            // Invia email di verifica
            try {
                $mail = new MailService();
                $mail->inviaVerificaEmail(
                    $this->authService->lastRegistrationEmail,
                    $this->authService->lastRegistrationNome,
                    $this->authService->lastRegistrationToken
                );
            } catch (Exception $mailEx) {
                // Ignora errori mail: l'utente potrà richiedere il reinvio
            }

            header('Location: index.php?route=verifica-email-attesa&email=' . urlencode($this->authService->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/registrazione_utente.php';
        }
    }


    public function showRegisterBusiness(): void
    {
        require __DIR__ . '/../views/utenti/registrazione_business.php';
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
            $idUtente = $this->authService->register($data);
            $this->businessService->creaAccount($data, $idUtente);

            $this->db->commit();

            // Invia email di verifica
            try {
                $mail = new MailService();
                $mail->inviaVerificaEmail(
                    $this->authService->lastRegistrationEmail,
                    $this->authService->lastRegistrationNome,
                    $this->authService->lastRegistrationToken
                );
            } catch (Exception $mailEx) {
                // Ignora errori mail
            }

            header('Location: index.php?route=verifica-email-attesa&email=' . urlencode($this->authService->lastRegistrationEmail));
            exit;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $errore = $e->getMessage();
            require __DIR__ . '/../views/utenti/registrazione_business.php';
        }
    }

    public function profilo(int $idUtente, string $filtroAnnunci = 'attivo'): void
    {
        $utente = $this->userService->findById($idUtente);
        $indirizziUtente = !empty($_SESSION['is_business'])
            ? []
            : $this->userService->getIndirizziByUserId($idUtente);
        $filtroAnnunci = $filtroAnnunci === 'venduto' ? 'venduto' : 'attivo';
        $annunciUtente = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
        $titoloAnnunciProfilo = $filtroAnnunci === 'venduto' ? 'Annunci venduti' : 'Annunci attivi';
        $cronologiaPagamenti = !empty($_SESSION['is_business'])
            ? []
            : $this->paymentService->getCronologiaByUserId($idUtente);

        require __DIR__ . '/../views/utenti/profilo.php';
    }

    public function venditore(int $idVenditore): void
    {
        if ($idVenditore <= 0) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $venditore = $this->userService->findById($idVenditore);
        $venditoreEntity = $venditore ? EUtenteRegistrato::fromArray($venditore) : null;

        if (!$venditoreEntity || $venditoreEntity->isBannato()) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $annunciVenditore = $this->annuncioService->getByUserIdAndStato($idVenditore, 'attivo');
        $feedbackVenditore = $this->feedbackService->getByVenditoreId($idVenditore);
        $mediaVenditore = $this->feedbackService->getMediaVoto($idVenditore);

        require __DIR__ . '/../views/utenti/venditore.php';
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
            $url = $this->userService->updatePropic($idUtente, $files['propic'] ?? []);
            $_SESSION['propic'] = $url;
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore          = $e->getMessage();
            $utente          = $this->userService->findById($idUtente);
            $indirizziUtente = !empty($_SESSION['is_business'])
                ? []
                : $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci   = 'attivo';
            $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti  = !empty($_SESSION['is_business'])
                ? []
                : $this->paymentService->getCronologiaByUserId($idUtente);
            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    public function aggiornaProfiloUtente(array $data, int $idUtente): void
    {
        try {
            $this->userService->aggiornaProfiloUtente($idUtente, $data);
            header('Location: index.php?route=profilo&profilo_aggiornato=1');
            exit;
        } catch (Exception $e) {
            $errore          = $e->getMessage();
            $utente          = $this->userService->findById($idUtente);
            $indirizziUtente = !empty($_SESSION['is_business']) ? [] : $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci   = 'attivo';
            $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti  = !empty($_SESSION['is_business']) ? [] : $this->paymentService->getCronologiaByUserId($idUtente);
            $openProfiloEdit = true;
            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    public function cambiaPassword(array $data, int $idUtente): void
    {
        try {
            $this->authService->cambiaPassword(
                $idUtente,
                $data['password_attuale']  ?? '',
                $data['nuova_password']    ?? '',
                $data['password_confirm']  ?? ''
            );
            header('Location: index.php?route=profilo&password_aggiornata=1');
            exit;
        } catch (Exception $e) {
            $errore          = $e->getMessage();
            $utente          = $this->userService->findById($idUtente);
            $indirizziUtente = !empty($_SESSION['is_business']) ? [] : $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci   = 'attivo';
            $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti  = !empty($_SESSION['is_business']) ? [] : $this->paymentService->getCronologiaByUserId($idUtente);
            $openPasswordEdit = true;
            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    public function salvaIndirizzoSpedizione(array $data, int $idUtente): void
    {
        try {
            $this->userService->updateIndirizzoSpedizione($idUtente, $data);

            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $utente = $this->userService->findById($idUtente);
            $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci = 'attivo';
            $annunciUtente = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';

            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    public function showModificaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        $editingIndirizzo = $this->userService->findIndirizzoByIdForUser($idIndirizzo, $idUtente);
        $editingIndirizzoEntity = $editingIndirizzo ? EIndirizzo::fromArray($editingIndirizzo) : null;

        if (!$editingIndirizzoEntity) {
            header('Location: index.php?route=profilo');
            exit;
        }

        $utente          = $this->userService->findById($idUtente);
        $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);
        $filtroAnnunci   = 'attivo';
        $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
        $titoloAnnunciProfilo = 'Annunci attivi';
        $cronologiaPagamenti  = $this->paymentService->getCronologiaByUserId($idUtente);

        require __DIR__ . '/../views/utenti/profilo.php';
    }

    public function aggiornaIndirizzo(array $data, int $idUtente): void
    {
        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        try {
            $this->userService->modificaIndirizzo($idIndirizzo, $idUtente, $data);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore          = $e->getMessage();
            $editingIndirizzo = $this->userService->findIndirizzoByIdForUser($idIndirizzo, $idUtente);
            $utente          = $this->userService->findById($idUtente);
            $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci   = 'attivo';
            $annunciUtente   = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti  = $this->paymentService->getCronologiaByUserId($idUtente);
            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    public function eliminaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->userService->eliminaIndirizzo($idIndirizzo, $idUtente);
        } catch (Exception $e) {
            // Ignora errori silenziosi, torna al profilo comunque
        }
        header('Location: index.php?route=profilo');
        exit;
    }

    public function impostaIndirizzoPredefinito(int $idIndirizzo, int $idUtente): void
    {
        try {
            $this->userService->setIndirizzoPredefinito($idUtente, $idIndirizzo);
            header('Location: index.php?route=profilo');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $utente = $this->userService->findById($idUtente);
            $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);
            $filtroAnnunci = 'attivo';
            $annunciUtente = $this->annuncioService->getByUserIdAndStato($idUtente, $filtroAnnunci);
            $titoloAnnunciProfilo = 'Annunci attivi';
            $cronologiaPagamenti = $this->paymentService->getCronologiaByUserId($idUtente);
            require __DIR__ . '/../views/utenti/profilo.php';
        }
    }

    // ----------------------------------------------------------------
    // Verifica email
    // ----------------------------------------------------------------

    public function verificaEmailAttesa(): void
    {
        $email = $_GET['email'] ?? '';
        require __DIR__ . '/../views/utenti/verifica_email_attesa.php';
    }

    public function verificaEmail(string $token): void
    {
        try {
            $this->authService->verificaEmail($token);
            $successo = 'Email verificata con successo! Ora puoi accedere.';
        } catch (Exception $e) {
            $errore = $e->getMessage();
        }
        require __DIR__ . '/../views/utenti/verifica_email.php';
    }

    public function reinviaVerifica(array $data): void
    {
        try {
            $mail = new MailService();
            $this->authService->reinviaVerifica($data['email'] ?? '', $mail);
            $successo = 'Se l\'email è registrata e non ancora verificata, riceverai un nuovo link.';
        } catch (Exception $e) {
            $errore = $e->getMessage();
        }
        require __DIR__ . '/../views/utenti/verifica_email_attesa.php';
    }

    // ----------------------------------------------------------------
    // Recupero password
    // ----------------------------------------------------------------

    public function showRecuperoPassword(): void
    {
        require __DIR__ . '/../views/utenti/recupero_password.php';
    }

    public function inviaResetPassword(array $data): void
    {
        try {
            $mail = new MailService();
            $this->authService->richiestaResetPassword($data['email'] ?? '', $mail);
        } catch (Exception $e) {
            // Silenziamo l'errore per non rivelare se l'email esiste
        }
        // Mostriamo sempre lo stesso messaggio di conferma
        $successo = 'Se l\'indirizzo è associato a un account, riceverai un\'email con le istruzioni.';
        require __DIR__ . '/../views/utenti/recupero_password.php';
    }

    public function showResetPassword(string $token): void
    {
        $idUtente = $this->authService->getResetTokenUserId($token);
        if ($idUtente === 0) {
            $errore = 'Il link non è valido o è scaduto.';
        }
        require __DIR__ . '/../views/utenti/reset_password.php';
    }

    public function resetPassword(array $data): void
    {
        $token = $data['token'] ?? '';
        try {
            $this->authService->resetPassword($token, $data['password'] ?? '', $data['password_confirm'] ?? '');
            header('Location: index.php?route=login&reset=ok');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $idUtente = $this->authService->getResetTokenUserId($token);
            require __DIR__ . '/../views/utenti/reset_password.php';
        }
    }

    private function renderLogin(string $errore = ''): void
    {
        $isEmailNonVerificata = str_starts_with($errore, 'EMAIL_NON_VERIFICATA:');
        $emailNonVerificata = $isEmailNonVerificata
            ? substr($errore, strlen('EMAIL_NON_VERIFICATA:'))
            : '';

        SmartyView::make()->render('utenti/login.tpl', [
            'resetOk' => ($_GET['reset'] ?? '') === 'ok',
            'errore' => $isEmailNonVerificata ? 'Email non verificata.' : $errore,
            'isEmailNonVerificata' => $isEmailNonVerificata,
            'emailNonVerificataUrl' => urlencode($emailNonVerificata),
        ], 'Login');
    }
}
