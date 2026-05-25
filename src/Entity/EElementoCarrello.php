<?php

namespace App\Entity;

class EElementoCarrello extends EBaseEntity
{
    private $idElementoCarrello;
    private $idCarrello;
    private $idAnnuncio;
    private $dataAggiunta;

    public function __construct(int $idCarrello = 0, int $idAnnuncio = 0)
    {
        $this->idCarrello = $idCarrello;
        $this->idAnnuncio = $idAnnuncio;
        $this->dataAggiunta = null;
    }

    public static function fromArray(array $data): self
    {
        $elemento = new self(
            (int) self::read($data, 'id_carrello', 'idCarrello', 0),
            (int) self::read($data, 'id_annuncio', 'idAnnuncio', 0)
        );
        $elemento->setIdElementoCarrello(self::intOrNull(self::read($data, 'id_elemento_carrello', 'idElementoCarrello')));
        $elemento->setDataAggiunta(self::read($data, 'data_aggiunta', 'dataAggiunta'));
        return $elemento;
    }

    public function getIdElementoCarrello(): ?int { return $this->idElementoCarrello; }
    public function setIdElementoCarrello(?int $idElementoCarrello): void { $this->idElementoCarrello = $idElementoCarrello; }
    public function getIdCarrello(): int { return $this->idCarrello; }
    public function setIdCarrello(int $idCarrello): void { $this->idCarrello = $idCarrello; }
    public function getIdAnnuncio(): int { return $this->idAnnuncio; }
    public function setIdAnnuncio(int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getDataAggiunta(): ?string { return $this->dataAggiunta; }
    public function setDataAggiunta(?string $dataAggiunta): void { $this->dataAggiunta = $dataAggiunta; }

    public function toArray(): array
    {
        return [
            'id_elemento_carrello' => $this->idElementoCarrello,
            'id_carrello' => $this->idCarrello,
            'id_annuncio' => $this->idAnnuncio,
            'data_aggiunta' => $this->dataAggiunta,
        ];
    }

    public function __toString(): string
    {
        return 'Elemento carrello #' . ($this->idElementoCarrello ?? 'nuovo') . ' - annuncio ' . $this->idAnnuncio;
    }
}
