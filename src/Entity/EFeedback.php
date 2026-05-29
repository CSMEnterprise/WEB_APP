<?php

namespace App\Entity;

/**
 * Rappresenta un feedback (recensione) lasciato dopo una transazione completata.
 *
 * Corrisponde alla tabella `feedback`.
 * Un feedback è sempre collegato a un pagamento completato (idPagamento)
 * e viene scritto dall'acquirente (idAutore) verso il venditore.
 * La valutazione va da 1 (minima) a 5 (massima).
 */
class EFeedback extends EBaseEntity
{
    private $idFeedback;
    /** ID dell'utente che ha scritto il feedback (l'acquirente) */
    private $idAutore;
    /** ID del pagamento a cui si riferisce il feedback */
    private $idPagamento;
    /** Voto da 1 a 5 stelle */
    private $valutazione;
    /** Testo opzionale del feedback */
    private $commento;
    private $dataFeedback;

    public function __construct(int $idAutore = 0, int $idPagamento = 0, int $valutazione = 1, ?string $commento = null)
    {
        $this->idAutore = $idAutore;
        $this->idPagamento = $idPagamento;
        $this->valutazione = $valutazione;
        $this->commento = $commento;
        $this->dataFeedback = null;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $feedback = new self(
            (int) self::read($data, 'id_autore', 'idAutore', 0),
            (int) self::read($data, 'id_pagamento', 'idPagamento', 0),
            (int) self::read($data, 'valutazione', 'valutazione', 1),
            self::read($data, 'commento', 'commento')
        );
        $feedback->setIdFeedback(self::intOrNull(self::read($data, 'id_feedback', 'idFeedback')));
        $feedback->setDataFeedback(self::read($data, 'data_feedback', 'dataFeedback'));
        $feedback->rememberExtra($data, array_keys($feedback->toArray()));

        return $feedback;
    }

    public function getIdFeedback(): ?int { return $this->idFeedback; }
    public function setIdFeedback(?int $idFeedback): void { $this->idFeedback = $idFeedback; }
    public function getIdAutore(): int { return $this->idAutore; }
    public function setIdAutore(int $idAutore): void { $this->idAutore = $idAutore; }
    public function getIdPagamento(): int { return $this->idPagamento; }
    public function setIdPagamento(int $idPagamento): void { $this->idPagamento = $idPagamento; }
    public function getValutazione(): int { return $this->valutazione; }
    public function setValutazione(int $valutazione): void { $this->valutazione = $valutazione; }
    public function getCommento(): ?string { return $this->commento; }
    public function setCommento(?string $commento): void { $this->commento = $commento; }
    public function getDataFeedback(): ?string { return $this->dataFeedback; }
    public function setDataFeedback(?string $dataFeedback): void { $this->dataFeedback = $dataFeedback; }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_feedback'  => $this->idFeedback,
            'id_autore'    => $this->idAutore,
            'id_pagamento' => $this->idPagamento,
            'valutazione'  => $this->valutazione,
            'commento'     => $this->commento,
            'data_feedback'=> $this->dataFeedback,
        ]);
    }

    public function __toString(): string
    {
        return 'Feedback #' . ($this->idFeedback ?? 'nuovo') . ' - voto ' . $this->valutazione;
    }
}
