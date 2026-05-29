<?php

namespace App\Entity;

/**
 * Rappresenta una segnalazione inviata da un utente alla piattaforma.
 *
 * Corrisponde alla tabella `segnalazione`.
 * Una segnalazione può riguardare un annuncio, un utente, un business o un feedback;
 * i campi relativi sono opzionali e mutuamente esclusivi (solo uno viene valorizzato).
 * Viene gestita da un admin che la chiude con una risoluzione.
 *
 * Stati possibili: 'Aperta', 'Risolta', 'Ignorata'.
 * Tipologie comuni: 'Contenuto inappropriato', 'Truffa', 'Spam', 'Altro'.
 */
class ESegnalazione extends EBaseEntity
{
    private $idSegnalazione;
    /** ID dell'utente che ha inviato la segnalazione */
    private $idSegnalante;
    /** ID dell'annuncio segnalato, null se non applicabile */
    private $idAnnuncio;
    /** ID dell'utente segnalato, null se non applicabile */
    private $idUtenteSegnalato;
    /** ID del business segnalato, null se non applicabile */
    private $idBusiness;
    /** ID del feedback segnalato, null se non applicabile */
    private $idFeedback;
    /** Categoria della segnalazione (es. 'Truffa', 'Spam', 'Altro') */
    private $tipologia;
    /** Testo descrittivo opzionale fornito dal segnalante */
    private $descrizione;
    /** Stato attuale: 'Aperta' | 'Risolta' | 'Ignorata' */
    private $stato;
    private $dataSegnalazione;
    /** ID dell'admin che ha gestito la segnalazione, null se ancora aperta */
    private $idAdmin;
    /** Data di chiusura della segnalazione, null se ancora aperta */
    private $dataRisoluzione;

    public function __construct(int $idSegnalante = 0, string $tipologia = 'Altro', ?string $descrizione = null)
    {
        $this->idSegnalante = $idSegnalante;
        $this->tipologia = $tipologia;
        $this->descrizione = $descrizione;
        $this->stato = 'Aperta';
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $segnalazione = new self(
            (int) self::read($data, 'id_segnalante', 'idSegnalante', 0),
            (string) self::read($data, 'tipologia', 'tipologia', 'Altro'),
            self::read($data, 'descrizione', 'descrizione')
        );

        $segnalazione->setIdSegnalazione(self::intOrNull(self::read($data, 'id_segnalazione', 'idSegnalazione')));
        $segnalazione->setIdAnnuncio(self::intOrNull(self::read($data, 'id_annuncio', 'idAnnuncio')));
        $segnalazione->setIdUtenteSegnalato(self::intOrNull(self::read($data, 'id_utente_segnalato', 'idUtenteSegnalato')));
        $segnalazione->setIdBusiness(self::intOrNull(self::read($data, 'id_business', 'idBusiness')));
        $segnalazione->setIdFeedback(self::intOrNull(self::read($data, 'id_feedback', 'idFeedback')));
        $segnalazione->setStato((string) self::read($data, 'stato', 'stato', 'Aperta'));
        $segnalazione->setDataSegnalazione(self::read($data, 'data_segnalazione', 'dataSegnalazione'));
        $segnalazione->setIdAdmin(self::intOrNull(self::read($data, 'id_admin', 'idAdmin')));
        $segnalazione->setDataRisoluzione(self::read($data, 'data_risoluzione', 'dataRisoluzione'));

        $segnalazione->rememberExtra($data, array_keys($segnalazione->toArray()));

        return $segnalazione;
    }

    public function getIdSegnalazione(): ?int { return $this->idSegnalazione; }
    public function setIdSegnalazione(?int $idSegnalazione): void { $this->idSegnalazione = $idSegnalazione; }
    public function getIdSegnalante(): int { return $this->idSegnalante; }
    public function setIdSegnalante(int $idSegnalante): void { $this->idSegnalante = $idSegnalante; }
    public function getIdAnnuncio(): ?int { return $this->idAnnuncio; }
    public function setIdAnnuncio(?int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getIdUtenteSegnalato(): ?int { return $this->idUtenteSegnalato; }
    public function setIdUtenteSegnalato(?int $idUtenteSegnalato): void { $this->idUtenteSegnalato = $idUtenteSegnalato; }
    public function getIdBusiness(): ?int { return $this->idBusiness; }
    public function setIdBusiness(?int $idBusiness): void { $this->idBusiness = $idBusiness; }
    public function getIdFeedback(): ?int { return $this->idFeedback; }
    public function setIdFeedback(?int $idFeedback): void { $this->idFeedback = $idFeedback; }
    public function getTipologia(): string { return $this->tipologia; }
    public function setTipologia(string $tipologia): void { $this->tipologia = $tipologia; }
    public function getDescrizione(): ?string { return $this->descrizione; }
    public function setDescrizione(?string $descrizione): void { $this->descrizione = $descrizione; }
    public function getStato(): string { return $this->stato; }
    public function setStato(string $stato): void { $this->stato = $stato; }
    public function getDataSegnalazione(): ?string { return $this->dataSegnalazione; }
    public function setDataSegnalazione(?string $dataSegnalazione): void { $this->dataSegnalazione = $dataSegnalazione; }
    public function getIdAdmin(): ?int { return $this->idAdmin; }
    public function setIdAdmin(?int $idAdmin): void { $this->idAdmin = $idAdmin; }
    public function getDataRisoluzione(): ?string { return $this->dataRisoluzione; }
    public function setDataRisoluzione(?string $dataRisoluzione): void { $this->dataRisoluzione = $dataRisoluzione; }

    public function isAperta(): bool
    {
        return $this->stato === 'Aperta';
    }

    /**
     * Chiude la segnalazione impostando lo stato a 'Risolta' e registrando
     * l'admin responsabile e la data di risoluzione.
     */
    public function chiudi(?int $idAdmin = null, ?string $dataRisoluzione = null): void
    {
        $this->stato = 'Risolta';
        $this->idAdmin = $idAdmin;
        $this->dataRisoluzione = $dataRisoluzione;
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_segnalazione'    => $this->idSegnalazione,
            'id_segnalante'      => $this->idSegnalante,
            'id_annuncio'        => $this->idAnnuncio,
            'id_utente_segnalato'=> $this->idUtenteSegnalato,
            'id_business'        => $this->idBusiness,
            'id_feedback'        => $this->idFeedback,
            'tipologia'          => $this->tipologia,
            'descrizione'        => $this->descrizione,
            'stato'              => $this->stato,
            'data_segnalazione'  => $this->dataSegnalazione,
            'id_admin'           => $this->idAdmin,
            'data_risoluzione'   => $this->dataRisoluzione,
        ]);
    }

    public function __toString(): string
    {
        return 'Segnalazione #' . ($this->idSegnalazione ?? 'nuova') . ' - ' . $this->tipologia . ' (' . $this->stato . ')';
    }
}
