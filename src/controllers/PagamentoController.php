<?php

require_once __DIR__ . '/../services/PaymentService.php';

class PagamentoController
{
    private PaymentService $paymentService;

    public function __construct(PDO $db)
    {
        $this->paymentService = new PaymentService($db);
    }

    public function checkout(int $idUtente, int $idAnnuncio): void
    {
        $this->paypalPlaceholder($idUtente, $idAnnuncio);
    }

    public function paypalPlaceholder(int $idUtente, int $idAnnuncio): void
    {
        try {
            $pagamento = $this->paymentService->preparaPagamento($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio'];
            $totale = $pagamento['totale'];
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

    public function paypalCancel(): void
    {
        header('Location: index.php?route=carrello&paypal=cancel');
        exit;
    }

    public function esito(): void
    {
        $status = $_GET['status'] ?? 'errore';
        require __DIR__ . '/../views/pagamenti/esito.php';
    }
}
