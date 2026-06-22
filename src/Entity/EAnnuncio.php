<?php

namespace App\Entity;

/**
 * Rappresenta un annuncio di vendita pubblicato sulla piattaforma.
 *
 * Corrisponde alla tabella `annuncio`.
 * Un annuncio può essere creato da un utente privato (idUtente) o da un account
 * business (idBusiness); solo uno dei due deve essere valorizzato.
 * Contiene una lista di EImmagine associate, popolata tramite fromArray() o addImmagine().
 *
 * Stati possibili: 'attivo', 'venduto', 'scaduto', 'rimosso'.
 */
class EAnnuncio extends EBaseEntity
{
    private $idAnnuncio;
    /** ID utente privato autore, null se l'annuncio è di un business */
    private $idUtente;
    /** ID account business autore, null se l'annuncio è di un privato */
    private $idBusiness;
    private $idCategoria;
    private $titolo;
    private $descrizione;
    /** Es. 'Nuovo', 'Ottimo', 'Buono', 'Accettabile', 'Da restaurare' */
    private $statoConservazione;
    private $prezzo;
    /** Es. 'Consegna', 'Ritiro', 'Entrambi' */
    private $modalitaConsegna;
    /** Stato corrente dell'annuncio: 'attivo' | 'venduto' | 'scaduto' | 'rimosso' */
    private $stato;
    private $dataCreazione;
    private $dataScadenza;
    /** @var EImmagine[] Immagini allegate, ordinate per campo `ordine` */
    private $immagini;

    public function __construct(
        int $idCategoria = 0,
        string $titolo = '',
        ?string $descrizione = null,
        string $statoConservazione = 'Nuovo',
        float $prezzo = 0.0
    ) {
        $this->idCategoria = $idCategoria;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->statoConservazione = $statoConservazione;
        $this->prezzo = $prezzo;
        $this->modalitaConsegna = 'Consegna';
        $this->stato = 'attivo';
        $this->immagini = [];
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $annuncio = new self(
            (int) self::read($data, 'id_categoria', 'idCategoria', 0),
            (string) self::read($data, 'titolo', 'titolo', ''),
            self::read($data, 'descrizione', 'descrizione'),
            (string) self::read($data, 'stato_conservazione', 'statoConservazione', 'Nuovo'),
            (float) self::read($data, 'prezzo', 'prezzo', 0)
        );

        $annuncio->setIdAnnuncio(self::intOrNull(self::read($data, 'id_annuncio', 'idAnnuncio')));
        $annuncio->setIdUtente(self::intOrNull(self::read($data, 'id_utente', 'idUtente')));
        $annuncio->setIdBusiness(self::intOrNull(self::read($data, 'id_business', 'idBusiness')));
        $annuncio->setModalitaConsegna((string) self::read($data, 'modalita_consegna', 'modalitaConsegna', 'Consegna'));
        $annuncio->setStato((string) self::read($data, 'stato', 'stato', 'attivo'));
        $annuncio->setDataCreazione(self::read($data, 'data_creazione', 'dataCreazione'));
        $annuncio->setDataScadenza(self::read($data, 'data_scadenza', 'dataScadenza'));

        foreach ((array) self::read($data, 'immagini', 'immagini', []) as $immagine) {
            $annuncio->addImmagine($immagine instanceof EImmagine ? $immagine : EImmagine::fromArray((array) $immagine));
        }

        $annuncio->rememberExtra($data, array_keys($annuncio->toArray()));

        return $annuncio;
    }

    public function getIdAnnuncio(): ?int { return $this->idAnnuncio; }
    public function setIdAnnuncio(?int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getIdUtente(): ?int { return $this->idUtente; }
    public function setIdUtente(?int $idUtente): void { $this->idUtente = $idUtente; }
    public function getIdBusiness(): ?int { return $this->idBusiness; }
    public function setIdBusiness(?int $idBusiness): void { $this->idBusiness = $idBusiness; }
    public function getIdCategoria(): int { return $this->idCategoria; }
    public function setIdCategoria(int $idCategoria): void { $this->idCategoria = $idCategoria; }
    public function getTitolo(): string { return $this->titolo; }
    public function setTitolo(string $titolo): void { $this->titolo = $titolo; }
    public function getDescrizione(): ?string { return $this->descrizione; }
    public function setDescrizione(?string $descrizione): void { $this->descrizione = $descrizione; }
    public function getStatoConservazione(): string { return $this->statoConservazione; }
    public function setStatoConservazione(string $statoConservazione): void { $this->statoConservazione = $statoConservazione; }
    public function getPrezzo(): float { return $this->prezzo; }
    public function setPrezzo(float $prezzo): void { $this->prezzo = $prezzo; }
    public function getModalitaConsegna(): string { return $this->modalitaConsegna; }
    public function setModalitaConsegna(string $modalitaConsegna): void { $this->modalitaConsegna = $modalitaConsegna; }
    public function getStato(): string { return $this->stato; }
    public function setStato(string $stato): void { $this->stato = $stato; }
    public function getDataCreazione(): ?string { return $this->dataCreazione; }
    public function setDataCreazione(?string $dataCreazione): void { $this->dataCreazione = $dataCreazione; }
    public function getDataScadenza(): ?string { return $this->dataScadenza; }
    public function setDataScadenza(?string $dataScadenza): void { $this->dataScadenza = $dataScadenza; }
    public function getImmagini(): array { return $this->immagini; }
    public function setImmagini(array $immagini): void { $this->immagini = $immagini; }

    public function isAttivo(): bool
    {
        return $this->stato === 'attivo';
    }

    public function isVenduto(): bool
    {
        return $this->stato === 'venduto';
    }

    public function setAttivo(): void
    {
        $this->stato = 'attivo';
    }

    public function setVenduto(): void
    {
        $this->stato = 'venduto';
    }

    /**
     * ID utente del venditore.
     * Per gli annunci business l'autore è un account business: il vero utente
     * venditore arriva dalla colonna calcolata `venditore_user_id` (JOIN),
     * conservata tra i campi extra. Per gli annunci privati coincide con id_utente.
     * Incapsula qui questa regola così i controller non devono conoscere il nome
     * della colonna di JOIN né leggerla da toArray().
     */
    public function getVenditoreUserId(): int
    {
        return (int) ($this->extra['venditore_user_id'] ?? $this->idUtente ?? 0);
    }

    /** Vero se l'annuncio è acquistabile dall'utente indicato: attivo e non suo. */
    public function isAcquistabileDa(int $idUtente): bool
    {
        return $this->isAttivo() && $this->getVenditoreUserId() !== $idUtente;
    }

    /** Aggiunge un'immagine alla lista. L'ordine è determinato dall'indice dell'array. */
    public function addImmagine(EImmagine $immagine): void
    {
        $this->immagini[] = $immagine;
    }

    /** Rimuove l'immagine alla posizione $pos e ricompatta l'array. */
    public function removeImmagine(int $pos): void
    {
        unset($this->immagini[$pos]);
        $this->immagini = array_values($this->immagini);
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_annuncio'        => $this->idAnnuncio,
            'id_utente'          => $this->idUtente,
            'id_business'        => $this->idBusiness,
            'id_categoria'       => $this->idCategoria,
            'titolo'             => $this->titolo,
            'descrizione'        => $this->descrizione,
            'stato_conservazione'=> $this->statoConservazione,
            'prezzo'             => $this->prezzo,
            'modalita_consegna'  => $this->modalitaConsegna,
            'stato'              => $this->stato,
            'data_creazione'     => $this->dataCreazione,
            'data_scadenza'      => $this->dataScadenza,
            'immagini'           => array_map(
                static fn($immagine) => $immagine instanceof EImmagine ? $immagine->toArray() : $immagine,
                $this->immagini
            ),
        ]);
    }

    public function __toString(): string
    {
        return 'Annuncio #' . ($this->idAnnuncio ?? 'nuovo') . ' - ' . $this->titolo . ' (' . $this->stato . ')';
    }
}
