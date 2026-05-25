<?php

require_once __DIR__ . '/../Entity/EIndirizzo.php';
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/CartService.php';

class PagamentoController
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
            $pagamento = $this->paymentService->preparaPagamento($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio'];
            $totale = $pagamento['totale'];
            $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);

            require __DIR__ . '/../views/pagamenti/checkout.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function paypalPlaceholder(int $idUtente, int $idAnnuncio, int $idIndirizzo = 0): void
    {
        try {
            $pagamento = $this->paymentService->preparaPagamento($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio'];
            $totale = $pagamento['totale'];
            $indirizzoSpedizione = $this->userService->findIndirizzoByIdForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizioneEntity = $indirizzoSpedizione ? EIndirizzo::fromArray($indirizzoSpedizione) : null;

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
            $carrello = $this->cartService->getCarrelloUtente($idUtente);
            $items = array_values(array_filter($carrello, static function ($item) use ($idUtente) {
                return ($item['stato'] ?? '') === 'attivo'
                    && (int)($item['id_utente'] ?? 0) !== $idUtente;
            }));

            if (empty($items)) {
                header('Location: index.php?route=carrello');
                exit;
            }

            $totale = array_sum(array_column($items, 'prezzo'));
            $indirizziUtente = $this->userService->getIndirizziByUserId($idUtente);

            require __DIR__ . '/../views/pagamenti/checkout_carrello.php';
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function paypalPlaceholderCarrello(int $idUtente, int $idIndirizzo): void
    {
        try {
            $carrello = $this->cartService->getCarrelloUtente($idUtente);
            $items = array_values(array_filter($carrello, static function ($item) use ($idUtente) {
                return ($item['stato'] ?? '') === 'attivo'
                    && (int)($item['id_utente'] ?? 0) !== $idUtente;
            }));

            if (empty($items)) {
                header('Location: index.php?route=carrello');
                exit;
            }

            $indirizzoSpedizione = $this->userService->findIndirizzoByIdForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizioneEntity = $indirizzoSpedizione ? EIndirizzo::fromArray($indirizzoSpedizione) : null;

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
