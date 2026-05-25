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

class PagamentoController extends BaseController
{
    private PaymentService $paymentService;
    private UserService    $userService;
    private CartService    $cartService;

    public function __construct(PDO $db)
    {
        $this->paymentService = new PaymentService($db);
        $this->userService    = new UserService($db);
        $this->cartService    = new CartService($db);
    }

    public function checkout(int $idUtente, int $idAnnuncio): void
    {
        try {
            $pagamento = $this->paymentService->preparaPagamentoEntity($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio']->toArray();
            $totale = $pagamento['totale'];
            $indirizziUtente = $this->entitiesToArrays($this->userService->getIndirizziByUserIdEntity($idUtente));

            require __DIR__ . '/../views/pagamenti/checkout.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function paypalPlaceholder(int $idUtente, int $idAnnuncio, int $idIndirizzo = 0): void
    {
        try {
            $pagamento = $this->paymentService->preparaPagamentoEntity($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio']->toArray();
            $totale = $pagamento['totale'];
            $indirizzoSpedizioneEntity = $this->userService->findIndirizzoEntityByIdForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizione = $this->entityToArray($indirizzoSpedizioneEntity);

            if (!$indirizzoSpedizioneEntity) {
                throw new ServiceException('Seleziona un indirizzo di spedizione valido.');
            }

            $paypalTransactionId = 'PAYPAL-SIM-' . date('YmdHis') . '-' . random_int(1000, 9999);

            require __DIR__ . '/../views/pagamenti/paypal_placeholder.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function conferma(array $data, int $idUtente): void
    {
        try {
            $idPagamento = $this->paymentService->confermaPagamento($data, $idUtente);
            header('Location: index.php?route=pagamento-esito&status=ok&id=' . $idPagamento);
            exit;
        } catch (Exception $e) {
            header('Location: index.php?route=pagamento-esito&status=errore');
            exit;
        }
    }

    public function checkoutCarrello(int $idUtente): void
    {
        try {
            $carrello = $this->entitiesToArrays($this->cartService->getCarrelloUtenteEntity($idUtente));
            $items = array_values(array_filter($carrello, static function ($item) use ($idUtente) {
                return ($item['stato'] ?? '') === 'attivo'
                    && (int)($item['id_utente'] ?? 0) !== $idUtente;
            }));

            if (empty($items)) {
                header('Location: index.php?route=carrello');
                exit;
            }

            $totale = array_sum(array_column($items, 'prezzo'));
            $indirizziUtente = $this->entitiesToArrays($this->userService->getIndirizziByUserIdEntity($idUtente));

            require __DIR__ . '/../views/pagamenti/checkout_carrello.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function paypalPlaceholderCarrello(int $idUtente, int $idIndirizzo): void
    {
        try {
            $carrello = $this->entitiesToArrays($this->cartService->getCarrelloUtenteEntity($idUtente));
            $items = array_values(array_filter($carrello, static function ($item) use ($idUtente) {
                return ($item['stato'] ?? '') === 'attivo'
                    && (int)($item['id_utente'] ?? 0) !== $idUtente;
            }));

            if (empty($items)) {
                header('Location: index.php?route=carrello');
                exit;
            }

            $indirizzoSpedizioneEntity = $this->userService->findIndirizzoEntityByIdForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizione = $this->entityToArray($indirizzoSpedizioneEntity);

            if (!$indirizzoSpedizioneEntity) {
                throw new \Exception('Seleziona un indirizzo di spedizione valido.');
            }

            $totale = array_sum(array_column($items, 'prezzo'));
            $paypalTransactionId = 'PAYPAL-SIM-' . date('YmdHis') . '-' . random_int(1000, 9999);

            require __DIR__ . '/../views/pagamenti/paypal_placeholder_carrello.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function confermaCarrello(array $data, int $idUtente): void
    {
        try {
            $idPagamenti = $this->paymentService->confermaPagamentoCarrello($data, $idUtente);
            header('Location: index.php?route=pagamento-esito&status=ok&id=' . end($idPagamenti) . '&n=' . count($idPagamenti));
            exit;
        } catch (Exception $e) {
            header('Location: index.php?route=pagamento-esito&status=errore');
            exit;
        }
    }

    public function paypalCancel(): void
    {
        header('Location: index.php?route=carrello&paypal=cancel');
        exit;
    }

    public function esito(): void
    {
        $status      = $_GET['status'] ?? 'errore';
        $idPagamento = (int) ($_GET['id'] ?? 0);
        require __DIR__ . '/../views/pagamenti/esito.php';
    }
}
