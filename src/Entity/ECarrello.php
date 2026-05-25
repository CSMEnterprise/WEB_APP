<?php

namespace App\Entity;

class ECarrello extends EBaseEntity
{
    private $idCarrello;
    private $idUtente;
    private $dataCreazione;
    private $dataAggiornamento;
    private $elementi;

    public function __construct(int $idUtente = 0)
    {
        $this->idUtente = $idUtente;
        $this->dataCreazione = null;
        $this->dataAggiornamento = null;
        $this->elementi = [];
    }

    public static function fromArray(array $data): self
    {
        $carrello = new self((int) self::read($data, 'id_utente', 'idUtente', 0));
        $carrello->setIdCarrello(self::intOrNull(self::read($data, 'id_carrello', 'idCarrello')));
        $carrello->setDataCreazione(self::read($data, 'data_creazione', 'dataCreazione'));
        $carrello->setDataAggiornamento(self::read($data, 'data_aggiornamento', 'dataAggiornamento'));

        foreach ((array) self::read($data, 'elementi', 'elementi', []) as $elemento) {
            $carrello->addElemento($elemento instanceof EElementoCarrello ? $elemento : EElementoCarrello::fromArray((array) $elemento));
        }

        return $carrello;
    }

    public function getIdCarrello(): ?int { return $this->idCarrello; }
    public function setIdCarrello(?int $idCarrello): void { $this->idCarrello = $idCarrello; }
    public function getIdUtente(): int { return $this->idUtente; }
    public function setIdUtente(int $idUtente): void { $this->idUtente = $idUtente; }
    public function getDataCreazione(): ?string { return $this->dataCreazione; }
    public function setDataCreazione(?string $dataCreazione): void { $this->dataCreazione = $dataCreazione; }
    public function getDataAggiornamento(): ?string { return $this->dataAggiornamento; }
    public function setDataAggiornamento(?string $dataAggiornamento): void { $this->dataAggiornamento = $dataAggiornamento; }
    public function getElementi(): array { return $this->elementi; }
    public function setElementi(array $elementi): void { $this->elementi = $elementi; }

    public function addElemento(EElementoCarrello $elemento): void
    {
        $this->elementi[] = $elemento;
    }

    public function removeElemento(int $pos): void
    {
        unset($this->elementi[$pos]);
        $this->elementi = array_values($this->elementi);
    }

    public function svuota(): void
    {
        $this->elementi = [];
    }

    public function toArray(): array
    {
        return [
            'id_carrello' => $this->idCarrello,
            'id_utente' => $this->idUtente,
            'data_creazione' => $this->dataCreazione,
            'data_aggiornamento' => $this->dataAggiornamento,
            'elementi' => array_map(static fn($elemento) => $elemento instanceof EElementoCarrello ? $elemento->toArray() : $elemento, $this->elementi),
        ];
    }

    public function __toString(): string
    {
        return 'Carrello #' . ($this->idCarrello ?? 'nuovo') . ' - utente ' . $this->idUtente;
    }
}
