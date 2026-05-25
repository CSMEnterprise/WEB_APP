<?php

namespace App\Entity;

class EModera extends EBaseEntity
{
    private $idModerazione;
    private $idAdmin;
    private $idUtente;
    private $idFeedback;
    private $idAnnuncio;
    private $idBusiness;
    private $azioneCompiuta;
    private $dataAzione;

    public function __construct(int $idAdmin = 0, string $azioneCompiuta = '')
    {
        $this->idAdmin = $idAdmin;
        $this->azioneCompiuta = $azioneCompiuta;
        $this->dataAzione = null;
    }

    public static function fromArray(array $data): self
    {
        $modera = new self(
            (int) self::read($data, 'id_admin', 'idAdmin', 0),
            (string) self::read($data, 'azione_compiuta', 'azioneCompiuta', '')
        );
        $modera->setIdModerazione(self::intOrNull(self::read($data, 'id_moderazione', 'idModerazione')));
        $modera->setIdUtente(self::intOrNull(self::read($data, 'id_utente', 'idUtente')));
        $modera->setIdFeedback(self::intOrNull(self::read($data, 'id_feedback', 'idFeedback')));
        $modera->setIdAnnuncio(self::intOrNull(self::read($data, 'id_annuncio', 'idAnnuncio')));
        $modera->setIdBusiness(self::intOrNull(self::read($data, 'id_business', 'idBusiness')));
        $modera->setDataAzione(self::read($data, 'data_azione', 'dataAzione'));
        return $modera;
    }

    public function getIdModerazione(): ?int { return $this->idModerazione; }
    public function setIdModerazione(?int $idModerazione): void { $this->idModerazione = $idModerazione; }
    public function getIdAdmin(): int { return $this->idAdmin; }
    public function setIdAdmin(int $idAdmin): void { $this->idAdmin = $idAdmin; }
    public function getIdUtente(): ?int { return $this->idUtente; }
    public function setIdUtente(?int $idUtente): void { $this->idUtente = $idUtente; }
    public function getIdFeedback(): ?int { return $this->idFeedback; }
    public function setIdFeedback(?int $idFeedback): void { $this->idFeedback = $idFeedback; }
    public function getIdAnnuncio(): ?int { return $this->idAnnuncio; }
    public function setIdAnnuncio(?int $idAnnuncio): void { $this->idAnnuncio = $idAnnuncio; }
    public function getIdBusiness(): ?int { return $this->idBusiness; }
    public function setIdBusiness(?int $idBusiness): void { $this->idBusiness = $idBusiness; }
    public function getAzioneCompiuta(): string { return $this->azioneCompiuta; }
    public function setAzioneCompiuta(string $azioneCompiuta): void { $this->azioneCompiuta = $azioneCompiuta; }
    public function getDataAzione(): ?string { return $this->dataAzione; }
    public function setDataAzione(?string $dataAzione): void { $this->dataAzione = $dataAzione; }

    public function toArray(): array
    {
        return [
            'id_moderazione' => $this->idModerazione,
            'id_admin' => $this->idAdmin,
            'id_utente' => $this->idUtente,
            'id_feedback' => $this->idFeedback,
            'id_annuncio' => $this->idAnnuncio,
            'id_business' => $this->idBusiness,
            'azione_compiuta' => $this->azioneCompiuta,
            'data_azione' => $this->dataAzione,
        ];
    }

    public function __toString(): string
    {
        return 'Moderazione #' . ($this->idModerazione ?? 'nuova') . ' - ' . $this->azioneCompiuta;
    }
}
