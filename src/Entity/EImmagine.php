<?php

namespace App\Entity;

/**
 * Rappresenta un'immagine allegata a un annuncio.
 *
 * Corrisponde alla tabella `immagine`.
 * Ogni annuncio può avere più immagini; il campo `ordine` determina
 * la sequenza di visualizzazione (0 = immagine principale/copertina).
 * Il campo `url` contiene il percorso relativo al file salvato sul server.
 */
class EImmagine extends EBaseEntity
{
    private $idImmagine;
    private $idAnnuncio;
    /** Percorso relativo al file immagine (es. 'uploads/annunci/foto.jpg') */
    private $url;
    /** Posizione nella galleria: 0 = copertina, valori crescenti = ordine successivo */
    private $ordine;

    public function __construct(int $idAnnuncio = 0, string $url = '', int $ordine = 0)
    {
        $this->idAnnuncio = $idAnnuncio;
        $this->url = $url;
        $this->ordine = $ordine;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
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
            'url'         => $this->url,
            'ordine'      => $this->ordine,
        ]);
    }

    public function __toString(): string
    {
        return 'Immagine #' . ($this->idImmagine ?? 'nuova') . ' - ' . $this->url;
    }
}
