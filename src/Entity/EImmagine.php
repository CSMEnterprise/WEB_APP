<?php

namespace App\Entity;

class EImmagine extends EBaseEntity
{
    private $idImmagine;
    private $idAnnuncio;
    private $url;
    private $ordine;

    public function __construct(int $idAnnuncio = 0, string $url = '', int $ordine = 0)
    {
        $this->idAnnuncio = $idAnnuncio;
        $this->url = $url;
        $this->ordine = $ordine;
    }

    public static function fromArray(array $data): self
    {
        $immagine = new self(
            (int) self::read($data, 'id_annuncio', 'idAnnuncio', 0),
            (string) self::read($data, 'url', 'url', ''),
            (int) self::read($data, 'ordine', 'ordine', 0)
        );
        $immagine->setIdImmagine(self::intOrNull(self::read($data, 'id_immagine', 'idImmagine')));
        $immagine->rememberExtra($data, array_keys($immagine->toArray()));

        return $immagine;
    }

    public function getIdImmagine(): ?int { return $this->idImmagine; }
    public function setIdImmagine(?int $idImmagine): void { $this->idImmagine = $idImmagine; }
    public function getIdAnnuncio(): int { return $this->idAnnuncio; }
    public function setIdAnnuncio(int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): void { $this->url = $url; }
    public function getOrdine(): int { return $this->ordine; }
    public function setOrdine(int $ordine): void { $this->ordine = $ordine; }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_immagine' => $this->idImmagine,
            'id_annuncio' => $this->idAnnuncio,
            'url' => $this->url,
            'ordine' => $this->ordine,
        ]);
    }

    public function __toString(): string
    {
        return 'Immagine #' . ($this->idImmagine ?? 'nuova') . ' - ' . $this->url;
    }
}
