<?php

namespace App\Entity;

class ESegnalazione extends EBaseEntity
{
    private $idSegnalazione;
    private $idSegnalante;
    private $idAnnuncio;
    private $idUtenteSegnalato;
    private $idBusiness;
    private $idFeedback;
    private $tipologia;
    private $descrizione;
    private $stato;
    private $dataSegnalazione;
    private $idAdmin;
    private $dataRisoluzione;

    public function __construct(int $idSegnalante = 0, string $tipologia = 'Altro', ?string $descrizione = null)
    {
        $this->idSegnalante = $idSegnalante;
        $this->tipologia = $tipologia;
        $this->descrizione = $descrizione;
        $this->stato = 'Aperta';
    }

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

    public function chiudi(?int $idAdmin = null, ?string $dataRisoluzione = null): void
    {
        $this->stato = 'Risolta';
        $this->idAdmin = $idAdmin;
        $this->dataRisoluzione = $dataRisoluzione;
    }

    public function toArray(): array
    {
        return [
            'id_segnalazione' => $this->idSegnalazione,
            'id_segnalante' => $this->idSegnalante,
            'id_annuncio' => $this->idAnnuncio,
            'id_utente_segnalato' => $this->idUtenteSegnalato,
            'id_business' => $this->idBusiness,
            'id_feedback' => $this->idFeedback,
            'tipologia' => $this->tipologia,
            'descrizione' => $this->descrizione,
            'stato' => $this->stato,
            'data_segnalazione' => $this->dataSegnalazione,
            'id_admin' => $this->idAdmin,
            'data_risoluzione' => $this->dataRisoluzione,
        ];
    }

    public function __toString(): string
    {
        return 'Segnalazione #' . ($this->idSegnalazione ?? 'nuova') . ' - ' . $this->tipologia . ' (' . $this->stato . ')';
    }
}
