<?php

namespace App\Entity;

/**
 * Rappresenta un pagamento effettuato per l'acquisto di un annuncio.
 *
 * Corrisponde alla tabella `pagamento`.
 * Il pagamento viene creato quando l'acquirente avvia il checkout e
 * completato dopo la conferma da parte di PayPal.
 * L'indirizzo di spedizione scelto viene bloccato al momento del pagamento.
 *
 * Stati possibili: 'In_attesa', 'Completato', 'Annullato'.
 */
class EPagamento extends EBaseEntity
{
    private $idPagamento;
    private $idAnnuncio;
    /** ID dell'utente acquirente */
    private $idAcquirente;
    /** ID dell'indirizzo scelto per la spedizione al momento del pagamento */
    private $idIndirizzoSpedizione;
    private $importoTotale;
    /** Stato del pagamento: 'In_attesa' | 'Completato' | 'Annullato' */
    private $stato;
    /** ID transazione restituito da PayPal dopo la conferma */
    private $paypalTransactionId;
    /** Data e ora del pagamento */
    private $data;

    public function __construct(int $idAnnuncio = 0, int $idAcquirente = 0, int $idIndirizzoSpedizione = 0, float $importoTotale = 0.0)
    {
        $this->idAnnuncio = $idAnnuncio;
        $this->idAcquirente = $idAcquirente;
        $this->idIndirizzoSpedizione = $idIndirizzoSpedizione;
        $this->importoTotale = $importoTotale;
        $this->stato = 'In_attesa';
        $this->paypalTransactionId = null;
        $this->data = null;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $pagamento = new self(
            (int) self::read($data, 'id_annuncio', 'idAnnuncio', 0),
            (int) self::read($data, 'id_acquirente', 'idAcquirente', 0),
            (int) self::read($data, 'id_indirizzo_spedizione', 'idIndirizzoSpedizione', 0),
            (float) self::read($data, 'importo_totale', 'importoTotale', 0)
        );
        $pagamento->setIdPagamento(self::intOrNull(self::read($data, 'id_pagamento', 'idPagamento')));
        $pagamento->setStato((string) self::read($data, 'stato', 'stato', 'In_attesa'));
        $pagamento->setPaypalTransactionId(self::read($data, 'paypal_transaction_id', 'paypalTransactionId'));
        $pagamento->setData(self::read($data, 'data', 'data'));
        $pagamento->rememberExtra($data, array_keys($pagamento->toArray()));

        return $pagamento;
    }

    public function getIdPagamento(): ?int { return $this->idPagamento; }
    public function setIdPagamento(?int $idPagamento): void { $this->idPagamento = $idPagamento; }
    public function getIdAnnuncio(): int { return $this->idAnnuncio; }
    public function setIdAnnuncio(int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getIdAcquirente(): int { return $this->idAcquirente; }
    public function setIdAcquirente(int $idAcquirente): void { $this->idAcquirente = $idAcquirente; }
    public function getIdIndirizzoSpedizione(): int { return $this->idIndirizzoSpedizione; }
    public function setIdIndirizzoSpedizione(int $idIndirizzoSpedizione): void { $this->idIndirizzoSpedizione = $idIndirizzoSpedizione; }
    public function getImportoTotale(): float { return $this->importoTotale; }
    public function setImportoTotale(float $importoTotale): void { $this->importoTotale = $importoTotale; }
    public function getStato(): string { return $this->stato; }
    public function setStato(string $stato): void { $this->stato = $stato; }
    public function getPaypalTransactionId(): ?string { return $this->paypalTransactionId; }
    public function setPaypalTransactionId(?string $paypalTransactionId): void { $this->paypalTransactionId = $paypalTransactionId; }
    public function getData(): ?string { return $this->data; }
    public function setData(?string $data): void { $this->data = $data; }

    public function isCompletato(): bool
    {
        return $this->stato === 'Completato';
    }

    /** Porta il pagamento allo stato 'Completato'. */
    public function completa(): void
    {
        $this->stato = 'Completato';
    }

    /** Porta il pagamento allo stato 'Annullato'. */
    public function annulla(): void
    {
        $this->stato = 'Annullato';
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_pagamento'           => $this->idPagamento,
            'id_annuncio'            => $this->idAnnuncio,
            'id_acquirente'          => $this->idAcquirente,
            'id_indirizzo_spedizione'=> $this->idIndirizzoSpedizione,
            'importo_totale'         => $this->importoTotale,
            'stato'                  => $this->stato,
            'paypal_transaction_id'  => $this->paypalTransactionId,
            'data'                   => $this->data,
        ]);
    }

    public function __toString(): string
    {
        return 'Pagamento #' . ($this->idPagamento ?? 'nuovo') . ' - ' . number_format($this->importoTotale, 2, '.', '') . ' EUR';
    }
}
