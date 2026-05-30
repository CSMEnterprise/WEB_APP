<?php

namespace App\Controllers;

use App\Entity\{
    EAnnuncio,
    EPagamento
};
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;
use Throwable;

/**
 * Gestisce checkout e pagamento simulato PayPal per singoli annunci e carrello.
 */
class PagamentoController extends BaseController
{
    private PDO $db;

    /**
     * Mantiene PDO per transazioni e query con lock sugli annunci.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Prepara la pagina di checkout per un singolo annuncio.
     */
    public function checkout(int $idUtente, int $idAnnuncio): void
    {
        try {
            $pagamento = $this->preparePayment($idUtente, $idAnnuncio);
            $annuncio = $this->entityToArray($pagamento['annuncio']);
            $totale = $pagamento['totale'];
            $indirizziUtente = $this->entitiesToArrays(FPersistentManager::indirizziByUser($idUtente));

            $this->view('pagamenti/checkout.tpl', compact('annuncio', 'totale', 'indirizziUtente'), 'Checkout');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Mostra la schermata PayPal simulata dopo la scelta dell'indirizzo.
     */
    public function paypalPlaceholder(int $idUtente, int $idAnnuncio, int $idIndirizzo = 0): void
    {
        try {
            $pagamento = $this->preparePayment($idUtente, $idAnnuncio);
            $annuncio = $this->entityToArray($pagamento['annuncio']);
            $totale = $pagamento['totale'];
            $indirizzoSpedizioneEntity = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizione = $this->entityToArray($indirizzoSpedizioneEntity);

            if (!$indirizzoSpedizioneEntity) {
                throw new ServiceException('Seleziona un indirizzo di spedizione valido.');
            }

            // ID fittizio: simula il codice transazione che arriverebbe da PayPal reale.
            $paypalTransactionId = 'PAYPAL-SIM-' . date('YmdHis') . '-' . random_int(1000, 9999);

            $this->view('pagamenti/paypal_placeholder.tpl', compact('annuncio', 'totale', 'indirizzoSpedizione', 'paypalTransactionId'), 'Pagamento PayPal');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Conferma il pagamento di un singolo annuncio e mostra pagina esito.
     */
    public function conferma(array $data, int $idUtente): void
    {
        try {
            $idPagamento = $this->confirmPayment($data, $idUtente);

            header('Location: /pagamento/esito?status=ok&id=' . $idPagamento);
            exit;
        } catch (Exception $e) {
            header('Location: /pagamento/esito?status=errore');
            exit;
        }
    }

    /**
     * Prepara checkout per tutti gli articoli acquistabili nel carrello.
     */
    public function checkoutCarrello(int $idUtente): void
    {
        try {
            $items = $this->getCarrelloAcquistabile($idUtente);

            if (empty($items)) {
                header('Location: /carrello/list');
                exit;
            }

            $totale = array_sum(array_column($items, 'prezzo'));
            $indirizziUtente = $this->entitiesToArrays(FPersistentManager::indirizziByUser($idUtente));

            $this->view('pagamenti/checkout_carrello.tpl', compact('items', 'totale', 'indirizziUtente'), 'Checkout carrello');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Mostra pagamento simulato PayPal per gli articoli acquistabili nel carrello.
     */
    public function paypalPlaceholderCarrello(int $idUtente, int $idIndirizzo): void
    {
        try {
            $items = $this->getCarrelloAcquistabile($idUtente);

            if (empty($items)) {
                header('Location: /carrello/list');
                exit;
            }

            $indirizzoSpedizioneEntity = FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente);
            $indirizzoSpedizione = $this->entityToArray($indirizzoSpedizioneEntity);

            if (!$indirizzoSpedizioneEntity) {
                throw new ServiceException('Seleziona un indirizzo di spedizione valido.');
            }

            $totale = array_sum(array_column($items, 'prezzo'));
            $paypalTransactionId = 'PAYPAL-SIM-' . date('YmdHis') . '-' . random_int(1000, 9999);

            $this->view('pagamenti/paypal_placeholder_carrello.tpl', compact('items', 'totale', 'indirizzoSpedizione', 'paypalTransactionId'), 'Pagamento PayPal');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Conferma piu pagamenti partendo dagli articoli selezionati nel carrello.
     */
    public function confermaCarrello(array $data, int $idUtente): void
    {
        try {
            $idPagamenti = $this->confirmCartPayment($data, $idUtente);

            header('Location: /pagamento/esito?status=ok&id=' . end($idPagamenti) . '&n=' . count($idPagamenti));
            exit;
        } catch (Exception $e) {
            header('Location: /pagamento/esito?status=errore');
            exit;
        }
    }

    /**
     * Gestisce annullamento pagamento simulato.
     */
    public function paypalCancel(): void
    {
        header('Location: /carrello/list?paypal=cancel');
        exit;
    }

    /**
     * Pagina finale con stato pagamento e numero operazioni completate.
     */
    public function esito(): void
    {
        $status = $_GET['status'] ?? 'errore';
        $idPagamento = (int) ($_GET['id'] ?? 0);
        $numeroPagamenti = (int) ($_GET['n'] ?? 0);

        $this->view('pagamenti/esito.tpl', compact('status', 'idPagamento', 'numeroPagamenti'), 'Esito pagamento');
    }

    /**
     * Controlli comuni prima di mostrare checkout/pagamento di un annuncio.
     */
    private function preparePayment(int $idUtente, int $idAnnuncio): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->denyBusinessBuyer($idUtente);

        $annuncio = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException('Annuncio non acquistabile.');
        }

        if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi acquistare un tuo annuncio.');
        }

        return ['annuncio' => $annuncio, 'totale' => $annuncio->getPrezzo()];
    }

    /**
     * Conferma un acquisto atomico: pagamento, stato venduto e pulizia carrelli/wishlist.
     */
    private function confirmPayment(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->denyBusinessBuyer($idUtente);

        $idAnnuncio = (int) ($data['id_annuncio'] ?? 0);
        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        $paypalTransactionId = $this->clean($data['paypal_transaction_id'] ?? '');

        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idIndirizzo, 'Indirizzo di spedizione');

        $this->db->beginTransaction();

        try {
            $annuncioRow = $this->getAnnuncioForPaymentUpdate($idAnnuncio);

            if (!$annuncioRow) {
                throw new ServiceException('Annuncio non trovato.');
            }

            $annuncio = EAnnuncio::fromArray($annuncioRow);

            if (!$annuncio->isAttivo()) {
                throw new ServiceException('Annuncio non acquistabile.');
            }

            if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
                throw new ServiceException('Non puoi acquistare un tuo annuncio.');
            }

            if (!FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente)) {
                throw new ServiceException('Indirizzo di spedizione non valido.');
            }

            $pagamento = new EPagamento($idAnnuncio, $idUtente, $idIndirizzo, $annuncio->getPrezzo());
            $pagamento->completa();
            $pagamento->setPaypalTransactionId($paypalTransactionId !== '' ? $paypalTransactionId : null);

            $idPagamento = FPersistentManager::createPagamento($pagamento);
            $this->markAnnuncioSoldForPayment($idAnnuncio);
            $this->removeAnnuncioFromBuyerSurfaces($idAnnuncio);

            $this->db->commit();

            return $idPagamento;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            if ($e instanceof ServiceException) {
                throw $e;
            }

            throw new ServiceException('Errore durante la conferma del pagamento.');
        }
    }

    /**
     * Conferma in blocco il carrello: gli articoli non piu acquistabili vengono saltati.
     */
    private function confirmCartPayment(array $data, int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->denyBusinessBuyer($idUtente);

        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        $this->requirePositiveId($idIndirizzo, 'Indirizzo di spedizione');

        $idAnnunci = array_map('intval', (array) ($data['id_annunci'] ?? []));
        $idAnnunci = array_filter($idAnnunci, static fn($id) => $id > 0);

        if (empty($idAnnunci)) {
            throw new ServiceException('Nessun articolo da acquistare.');
        }

        if (!FPersistentManager::indirizzoForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo di spedizione non valido.');
        }

        $this->db->beginTransaction();

        try {
            $idPagamenti = [];

            foreach ($idAnnunci as $idAnnuncio) {
                // Ogni annuncio viene letto con lock per evitare doppie vendite concorrenti.
                $annuncioRow = $this->getAnnuncioForPaymentUpdate($idAnnuncio);
                $annuncio = $annuncioRow ? EAnnuncio::fromArray($annuncioRow) : null;

                if (!$annuncio || !$annuncio->isAttivo()) {
                    continue;
                }

                if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
                    continue;
                }

                $pagamento = new EPagamento($idAnnuncio, $idUtente, $idIndirizzo, $annuncio->getPrezzo());
                $pagamento->completa();

                $idPagamenti[] = FPersistentManager::createPagamento($pagamento);
                $this->markAnnuncioSoldForPayment($idAnnuncio);
                $this->removeAnnuncioFromBuyerSurfaces($idAnnuncio);
            }

            if (empty($idPagamenti)) {
                throw new ServiceException('Nessun articolo acquistabile nel carrello.');
            }

            $this->db->commit();

            return $idPagamenti;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            if ($e instanceof ServiceException) {
                throw $e;
            }

            throw new ServiceException('Errore durante la conferma del pagamento.');
        }
    }

    /**
     * Restituisce solo righe acquistabili, togliendo prima quelle obsolete dal carrello.
     */
    private function getCarrelloAcquistabile(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->denyBusinessBuyer($idUtente);

        $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
        $annunciRimossi = FPersistentManager::unavailableCartItems($idCarrello);

        if (!empty($annunciRimossi)) {
            FPersistentManager::removeUnavailableCartItems($idCarrello);
        }

        $carrello = $this->entitiesToArrays(FPersistentManager::elementiCarrelloAcquistabili($idCarrello));

        return array_values(array_filter($carrello, static function ($item) use ($idUtente) {
            return ($item['stato'] ?? '') === 'attivo'
                && (int)($item['id_utente'] ?? 0) !== $idUtente;
        }));
    }

    /**
     * Legge l'annuncio con FOR UPDATE per bloccarlo durante la transazione di pagamento.
     */
    private function getAnnuncioForPaymentUpdate(int $idAnnuncio): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.id_annuncio = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([$idAnnuncio]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Marca l'annuncio come venduto solo se era ancora attivo.
     */
    private function markAnnuncioSoldForPayment(int $idAnnuncio): void
    {
        $stmt = $this->db->prepare("
            UPDATE annuncio
            SET stato = 'venduto'
            WHERE id_annuncio = ? AND stato = 'attivo'
        ");
        $stmt->execute([$idAnnuncio]);

        if ($stmt->rowCount() !== 1) {
            throw new ServiceException('Annuncio non acquistabile.');
        }
    }

    /**
     * Dopo la vendita rimuove l'annuncio da carrelli e wishlist di tutti gli utenti.
     */
    private function removeAnnuncioFromBuyerSurfaces(int $idAnnuncio): void
    {
        FPersistentManager::removeAnnuncioFromAllCarts($idAnnuncio);
        FPersistentManager::removePreferitiByAnnuncio($idAnnuncio);
    }
}
