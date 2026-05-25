<?php

namespace App\Entity;

class EUtenteRegistrato extends EBaseEntity
{
    private $idUtente;
    private $email;
    private $emailVerificata;
    private $tokenVerifica;
    private $tokenScadenza;
    private $username;
    private $passwordHash;
    private $nome;
    private $telefono;
    private $propic;
    private $statoBan;
    private $dataRegistrazione;

    public function __construct(
        string $email = '',
        string $username = '',
        string $passwordHash = '',
        ?string $nome = null,
        ?string $telefono = null
    ) {
        $this->email = $email;
        $this->emailVerificata = false;
        $this->tokenVerifica = null;
        $this->tokenScadenza = null;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->nome = $nome;
        $this->telefono = $telefono;
        $this->propic = null;
        $this->statoBan = false;
        $this->dataRegistrazione = null;
    }

    public static function fromArray(array $data): self
    {
        $utente = new self(
            (string) self::read($data, 'email', 'email', ''),
            (string) self::read($data, 'username', 'username', ''),
            (string) self::read($data, 'password_hash', 'passwordHash', ''),
            self::read($data, 'nome', 'nome'),
            self::read($data, 'telefono', 'telefono')
        );

        $utente->setIdUtente(self::intOrNull(self::read($data, 'id_utente', 'idUtente')));
        $utente->setEmailVerificata(self::boolFromDb(self::read($data, 'email_verificata', 'emailVerificata', false)));
        $utente->setTokenVerifica(self::read($data, 'token_verifica', 'tokenVerifica'));
        $utente->setTokenScadenza(self::read($data, 'token_scadenza', 'tokenScadenza'));
        $utente->setPropic(self::read($data, 'propic', 'propic'));
        $utente->setStatoBan(self::boolFromDb(self::read($data, 'stato_ban', 'statoBan', false)));
        $utente->setDataRegistrazione(self::read($data, 'data_registrazione', 'dataRegistrazione'));

        return $utente;
    }

    public function getIdUtente(): ?int
    {
        return $this->idUtente;
    }

    public function setIdUtente(?int $idUtente): void
    {
        $this->idUtente = $idUtente;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmailVerificata(): bool
    {
        return $this->emailVerificata;
    }

    public function isEmailVerificata(): bool
    {
        return $this->emailVerificata;
    }

    public function setEmailVerificata(bool $emailVerificata): void
    {
        $this->emailVerificata = $emailVerificata;
    }

    public function getTokenVerifica(): ?string
    {
        return $this->tokenVerifica;
    }

    public function setTokenVerifica(?string $tokenVerifica): void
    {
        $this->tokenVerifica = $tokenVerifica;
    }

    public function getTokenScadenza(): ?string
    {
        return $this->tokenScadenza;
    }

    public function setTokenScadenza(?string $tokenScadenza): void
    {
        $this->tokenScadenza = $tokenScadenza;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getPropic(): ?string
    {
        return $this->propic;
    }

    public function setPropic(?string $propic): void
    {
        $this->propic = $propic;
    }

    public function getStatoBan(): bool
    {
        return $this->statoBan;
    }

    public function isBannato(): bool
    {
        return $this->statoBan;
    }

    public function setStatoBan(bool $statoBan): void
    {
        $this->statoBan = $statoBan;
    }

    public function banna(): void
    {
        $this->statoBan = true;
    }

    public function sblocca(): void
    {
        $this->statoBan = false;
    }

    public function getDataRegistrazione(): ?string
    {
        return $this->dataRegistrazione;
    }

    public function setDataRegistrazione(?string $dataRegistrazione): void
    {
        $this->dataRegistrazione = $dataRegistrazione;
    }

    public function toArray(): array
    {
        return [
            'id_utente' => $this->idUtente,
            'email' => $this->email,
            'email_verificata' => self::boolToDb($this->emailVerificata),
            'token_verifica' => $this->tokenVerifica,
            'token_scadenza' => $this->tokenScadenza,
            'username' => $this->username,
            'password_hash' => $this->passwordHash,
            'nome' => $this->nome,
            'telefono' => $this->telefono,
            'propic' => $this->propic,
            'stato_ban' => self::boolToDb($this->statoBan),
            'data_registrazione' => $this->dataRegistrazione,
        ];
    }

    public function __toString(): string
    {
        return 'Utente #' . ($this->idUtente ?? 'nuovo') . ' - ' . $this->username;
    }
}
