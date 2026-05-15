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
        try {
            $pagamento = $this->paymentService->preparaPagamento($idUtente, $idAnnuncio);
            $annuncio = $pagamento['annuncio'];
            $totale = $pagamento['totale'];

            require __DIR__ . '/../views/pagamenti/checkout.php';
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

    public function esito(): void
    {
        $status      = $_GET['status'] ?? 'errore';
        $idPagamento = (int) ($_GET['id'] ?? 0);
        require __DIR__ . '/../views/pagamenti/esito.php';
    }
}
