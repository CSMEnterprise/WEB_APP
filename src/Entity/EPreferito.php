<?php

namespace App\Entity;

/**
 * Rappresenta un annuncio salvato nei preferiti da un utente.
 *
 * Corrisponde alla tabella `preferito`.
 * La chiave primaria è composta da (idUtente, idAnnuncio),
 * quindi ogni annuncio può essere messo nei preferiti una sola volta per utente.
 */
class EPreferito extends EBaseEntity
{
    private $idUtente;
    private $idAnnuncio;
    /** Data e ora in cui l'annuncio è stato aggiunto ai preferiti */
    private $dataAggiunta;

    public function __construct(int $idUtente = 0, int $idAnnuncio = 0)
    {
        $this->idUtente = $idUtente;
        $this->idAnnuncio = $idAnnuncio;
        $this->dataAggiunta = null;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $preferito = new self(
            (int) self::read($data, 'id_utente', 'idUtente', 0),
            (int) self::read($data, 'id_annuncio', 'idAnnuncio', 0)
        );
        $preferito->setDataAggiunta(self::read($data, 'data_aggiunta', 'dataAggiunta'));
        $preferito->rememberExtra($data, array_keys($preferito->toArray()));

        return $preferito;
    }

    public function getIdUtente(): int { return $this->idUtente; }
    public function setIdUtente(int $idUtente): void { $this->idUtente = $idUtente; }
    public function getIdAnnuncio(): int { return $this->idAnnuncio; }
    public function setIdAnnuncio(int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getDataAggiunta(): ?string { return $this->dataAggiunta; }
    public function setDataAggiunta(?string $dataAggiunta): void { $this->dataAggiunta = $dataAggiunta; }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_utente'   => $this->idUtente,
            'id_annuncio' => $this->idAnnuncio,
            'data_aggiunta'=> $this->dataAggiunta,
        ]);
    }

    public function __toString(): string
    {
        return 'Preferito utente ' . $this->idUtente . ' - annuncio ' . $this->idAnnuncio;
    }
}
