<?php

namespace App\Entity;

class EFeedback extends EBaseEntity
{
    private $idFeedback;
    private $idAutore;
    private $idPagamento;
    private $valutazione;
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
            'id_feedback' => $this->idFeedback,
            'id_autore' => $this->idAutore,
            'id_pagamento' => $this->idPagamento,
            'valutazione' => $this->valutazione,
            'commento' => $this->commento,
            'data_feedback' => $this->dataFeedback,
        ]);
    }

    public function __toString(): string
    {
        return 'Feedback #' . ($this->idFeedback ?? 'nuovo') . ' - voto ' . $this->valutazione;
    }
}
