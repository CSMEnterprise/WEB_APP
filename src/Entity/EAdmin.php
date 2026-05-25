<?php

namespace App\Entity;

class EAdmin extends EBaseEntity
{
    private $idAdmin;
    private $email;
    private $passwordHash;
    private $livelloSicurezza;
    private $statoBan;
    private $dataCreazione;

    public function __construct(
        string $email = '',
        string $passwordHash = '',
        int $livelloSicurezza = 1,
        bool $statoBan = false,
        ?string $dataCreazione = null
    ) {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->livelloSicurezza = $livelloSicurezza;
        $this->statoBan = $statoBan;
        $this->dataCreazione = $dataCreazione;
    }

    public static function fromArray(array $data): self
    {
        $admin = new self(
            (string) self::read($data, 'email', 'email', ''),
            (string) self::read($data, 'password_hash', 'passwordHash', ''),
            (int) self::read($data, 'livello_sicurezza', 'livelloSicurezza', 1),
            self::boolFromDb(self::read($data, 'stato_ban', 'statoBan', false)),
            self::read($data, 'data_creazione', 'dataCreazione')
        );

        $admin->setIdAdmin(self::intOrNull(self::read($data, 'id_admin', 'idAdmin')));
        return $admin;
    }

    public function getIdAdmin(): ?int
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(?int $idAdmin): void
    {
        $this->idAdmin = $idAdmin;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getLivelloSicurezza(): int
    {
        return $this->livelloSicurezza;
    }

    public function setLivelloSicurezza(int $livelloSicurezza): void
    {
        $this->livelloSicurezza = $livelloSicurezza;
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

    public function getDataCreazione(): ?string
    {
        return $this->dataCreazione;
    }

    public function setDataCreazione(?string $dataCreazione): void
    {
        $this->dataCreazione = $dataCreazione;
    }

    public function toArray(): array
    {
        return [
            'id_admin' => $this->idAdmin,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'livello_sicurezza' => $this->livelloSicurezza,
            'stato_ban' => self::boolToDb($this->statoBan),
            'data_creazione' => $this->dataCreazione,
        ];
    }

    public function __toString(): string
    {
        return 'Admin #' . ($this->idAdmin ?? 'nuovo') . ' - ' . $this->email;
    }
}
