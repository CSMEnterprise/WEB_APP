<?php

namespace App\Entity;

class EIndirizzo extends EBaseEntity
{
    private $idIndirizzo;
    private $idUtente;
    private $idBusiness;
    private $tipo;
    private $via;
    private $numero;
    private $cap;
    private $citta;
    private $provincia;
    private $paese;
    private $predefinito;

    public function __construct(?int $idUtente = null, ?int $idBusiness = null, string $via = '', string $citta = '')
    {
        $this->idUtente = $idUtente;
        $this->idBusiness = $idBusiness;
        $this->tipo = 'casa';
        $this->via = $via;
        $this->numero = null;
        $this->cap = null;
        $this->citta = $citta;
        $this->provincia = null;
        $this->paese = 'Italia';
        $this->predefinito = false;
    }

    public static function fromArray(array $data): self
    {
        $indirizzo = new self(
            self::intOrNull(self::read($data, 'id_utente', 'idUtente')),
            self::intOrNull(self::read($data, 'id_business', 'idBusiness')),
            (string) self::read($data, 'via', 'via', ''),
            (string) self::read($data, 'citta', 'citta', '')
        );

        $indirizzo->setIdIndirizzo(self::intOrNull(self::read($data, 'id_indirizzo', 'idIndirizzo')));
        $indirizzo->setTipo((string) self::read($data, 'tipo', 'tipo', 'casa'));
        $indirizzo->setNumero(self::read($data, 'numero', 'numero'));
        $indirizzo->setCap(self::read($data, 'cap', 'cap'));
        $indirizzo->setProvincia(self::read($data, 'provincia', 'provincia'));
        $indirizzo->setPaese((string) self::read($data, 'paese', 'paese', 'Italia'));
        $indirizzo->setPredefinito(self::boolFromDb(self::read($data, 'predefinito', 'predefinito', false)));

        $indirizzo->rememberExtra($data, array_keys($indirizzo->toArray()));


        return $indirizzo;
    }

    public function getIdIndirizzo(): ?int { return $this->idIndirizzo; }
    public function setIdIndirizzo(?int $idIndirizzo): void { $this->idIndirizzo = $idIndirizzo; }
    public function getIdUtente(): ?int { return $this->idUtente; }
    public function setIdUtente(?int $idUtente): void { $this->idUtente = $idUtente; }
    public function getIdBusiness(): ?int { return $this->idBusiness; }
    public function setIdBusiness(?int $idBusiness): void { $this->idBusiness = $idBusiness; }
    public function getTipo(): string { return $this->tipo; }
    public function setTipo(string $tipo): void { $this->tipo = $tipo; }
    public function getVia(): string { return $this->via; }
    public function setVia(string $via): void { $this->via = $via; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(?string $numero): void { $this->numero = $numero; }
    public function getCap(): ?string { return $this->cap; }
    public function setCap(?string $cap): void { $this->cap = $cap; }
    public function getCitta(): string { return $this->citta; }
    public function setCitta(string $citta): void { $this->citta = $citta; }
    public function getProvincia(): ?string { return $this->provincia; }
    public function setProvincia(?string $provincia): void { $this->provincia = $provincia; }
    public function getPaese(): string { return $this->paese; }
    public function setPaese(string $paese): void { $this->paese = $paese; }
    public function getPredefinito(): bool { return $this->predefinito; }
    public function isPredefinito(): bool { return $this->predefinito; }
    public function setPredefinito(bool $predefinito): void { $this->predefinito = $predefinito; }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_indirizzo' => $this->idIndirizzo,
            'id_utente' => $this->idUtente,
            'id_business' => $this->idBusiness,
            'tipo' => $this->tipo,
            'via' => $this->via,
            'numero' => $this->numero,
            'cap' => $this->cap,
            'citta' => $this->citta,
            'provincia' => $this->provincia,
            'paese' => $this->paese,
            'predefinito' => self::boolToDb($this->predefinito),
        ]);
    }

    public function __toString(): string
    {
        return trim($this->via . ' ' . ($this->numero ?? '') . ', ' . $this->citta);
    }
}
