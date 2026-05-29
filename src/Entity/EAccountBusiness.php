<?php

namespace App\Entity;

/**
 * Rappresenta il profilo business di un utente registrato.
 *
 * Corrisponde alla tabella `account_business`.
 * Un account business è sempre collegato a un EUtenteRegistrato tramite idUtente
 * e deve essere verificato da un admin prima di poter pubblicare annunci aziendali.
 */
class EAccountBusiness extends EBaseEntity
{
    private $idAccBusiness;
    /** ID dell'utente proprietario del profilo business */
    private $idUtente;
    /** Partita IVA dell'azienda */
    private $pIva;
    private $nomeAzienda;
    /** Percorso relativo al logo aziendale, null se non caricato */
    private $logo;
    private $descrizione;
    private $telefono;
    private $emailAziendale;
    /** URL del profilo social (es. Instagram, Facebook), opzionale */
    private $linkSocial;
    /** true se un admin ha verificato la validità del profilo business */
    private $verificato;
    private $dataRegistrazione;
    /** ID dell'admin che ha effettuato la verifica, null se non ancora verificato */
    private $idAdminVerifica;
    /** Data in cui è avvenuta la verifica, null se non ancora verificato */
    private $dataVerifica;

    public function __construct(
        int $idUtente = 0,
        string $pIva = '',
        string $nomeAzienda = '',
        string $emailAziendale = ''
    ) {
        $this->idUtente = $idUtente;
        $this->pIva = $pIva;
        $this->nomeAzienda = $nomeAzienda;
        $this->logo = null;
        $this->descrizione = null;
        $this->telefono = null;
        $this->emailAziendale = $emailAziendale;
        $this->linkSocial = null;
        $this->verificato = false;
        $this->dataRegistrazione = null;
        $this->idAdminVerifica = null;
        $this->dataVerifica = null;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $business = new self(
            (int) self::read($data, 'id_utente', 'idUtente', 0),
            (string) self::read($data, 'p_iva', 'pIva', ''),
            (string) self::read($data, 'nome_azienda', 'nomeAzienda', ''),
            (string) self::read($data, 'email_aziendale', 'emailAziendale', '')
        );

        $business->setIdAccBusiness(self::intOrNull(self::read($data, 'id_acc_business', 'idAccBusiness')));
        $business->setLogo(self::read($data, 'logo', 'logo'));
        $business->setDescrizione(self::read($data, 'descrizione', 'descrizione'));
        $business->setTelefono(self::read($data, 'telefono', 'telefono'));
        $business->setLinkSocial(self::read($data, 'link_social', 'linkSocial'));
        $business->setVerificato(self::boolFromDb(self::read($data, 'verificato', 'verificato', false)));
        $business->setDataRegistrazione(self::read($data, 'data_registrazione', 'dataRegistrazione'));
        $business->setIdAdminVerifica(self::intOrNull(self::read($data, 'id_admin_verifica', 'idAdminVerifica')));
        $business->setDataVerifica(self::read($data, 'data_verifica', 'dataVerifica'));

        $business->rememberExtra($data, array_keys($business->toArray()));

        return $business;
    }

    public function getIdAccBusiness(): ?int { return $this->idAccBusiness; }
    public function setIdAccBusiness(?int $idAccBusiness): void { $this->idAccBusiness = $idAccBusiness; }
    public function getIdUtente(): int { return $this->idUtente; }
    public function setIdUtente(int $idUtente): void { $this->idUtente = $idUtente; }
    public function getPIva(): string { return $this->pIva; }
    public function setPIva(string $pIva): void { $this->pIva = $pIva; }
    public function getNomeAzienda(): string { return $this->nomeAzienda; }
    public function setNomeAzienda(string $nomeAzienda): void { $this->nomeAzienda = $nomeAzienda; }
    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $logo): void { $this->logo = $logo; }
    public function getDescrizione(): ?string { return $this->descrizione; }
    public function setDescrizione(?string $descrizione): void { $this->descrizione = $descrizione; }
    public function getTelefono(): ?string { return $this->telefono; }
    public function setTelefono(?string $telefono): void { $this->telefono = $telefono; }
    public function getEmailAziendale(): string { return $this->emailAziendale; }
    public function setEmailAziendale(string $emailAziendale): void { $this->emailAziendale = $emailAziendale; }
    public function getLinkSocial(): ?string { return $this->linkSocial; }
    public function setLinkSocial(?string $linkSocial): void { $this->linkSocial = $linkSocial; }
    public function getVerificato(): bool { return $this->verificato; }
    public function isVerificato(): bool { return $this->verificato; }
    public function setVerificato(bool $verificato): void { $this->verificato = $verificato; }
    public function getDataRegistrazione(): ?string { return $this->dataRegistrazione; }
    public function setDataRegistrazione(?string $dataRegistrazione): void { $this->dataRegistrazione = $dataRegistrazione; }
    public function getIdAdminVerifica(): ?int { return $this->idAdminVerifica; }
    public function setIdAdminVerifica(?int $idAdminVerifica): void { $this->idAdminVerifica = $idAdminVerifica; }
    public function getDataVerifica(): ?string { return $this->dataVerifica; }
    public function setDataVerifica(?string $dataVerifica): void { $this->dataVerifica = $dataVerifica; }

    /**
     * Segna il business come verificato, registrando l'admin responsabile e la data.
     * Passare null per entrambi se la verifica è automatica.
     */
    public function verifica(?int $idAdminVerifica = null, ?string $dataVerifica = null): void
    {
        $this->verificato = true;
        $this->idAdminVerifica = $idAdminVerifica;
        $this->dataVerifica = $dataVerifica;
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_acc_business'   => $this->idAccBusiness,
            'id_utente'         => $this->idUtente,
            'p_iva'             => $this->pIva,
            'nome_azienda'      => $this->nomeAzienda,
            'logo'              => $this->logo,
            'descrizione'       => $this->descrizione,
            'telefono'          => $this->telefono,
            'email_aziendale'   => $this->emailAziendale,
            'link_social'       => $this->linkSocial,
            'verificato'        => self::boolToDb($this->verificato),
            'data_registrazione'=> $this->dataRegistrazione,
            'id_admin_verifica' => $this->idAdminVerifica,
            'data_verifica'     => $this->dataVerifica,
        ]);
    }

    public function __toString(): string
    {
        return 'Business #' . ($this->idAccBusiness ?? 'nuovo') . ' - ' . $this->nomeAzienda;
    }
}
