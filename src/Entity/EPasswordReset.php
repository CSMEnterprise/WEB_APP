<?php

namespace App\Entity;

/**
 * Rappresenta una richiesta di reset password per un utente.
 *
 * Corrisponde alla tabella `password_reset`.
 * Ogni richiesta genera un token univoco con una scadenza temporale.
 * Il token può essere usato una sola volta: dopo l'utilizzo `usato` viene
 * impostato a true e il token non è più accettato.
 */
class EPasswordReset extends EBaseEntity
{
    private $idReset;
    private $idUtente;
    /** Token casuale (es. UUID o hash) inviato via email all'utente */
    private $token;
    /** Data e ora di scadenza del token (formato stringa dal DB) */
    private $scadenza;
    /** true se il token è già stato usato per reimpostare la password */
    private $usato;
    private $creatoIl;

    public function __construct(int $idUtente = 0, string $token = '', string $scadenza = '')
    {
        $this->idUtente = $idUtente;
        $this->token = $token;
        $this->scadenza = $scadenza;
        $this->usato = false;
        $this->creatoIl = null;
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
    public static function fromArray(array $data): self
    {
        $reset = new self(
            (int) self::read($data, 'id_utente', 'idUtente', 0),
            (string) self::read($data, 'token', 'token', ''),
            (string) self::read($data, 'scadenza', 'scadenza', '')
        );
        $reset->setIdReset(self::intOrNull(self::read($data, 'id_reset', 'idReset')));
        $reset->setUsato(self::boolFromDb(self::read($data, 'usato', 'usato', false)));
        $reset->setCreatoIl(self::read($data, 'creato_il', 'creatoIl'));
        $reset->rememberExtra($data, array_keys($reset->toArray()));

        return $reset;
    }

    public function getIdReset(): ?int { return $this->idReset; }
    public function setIdReset(?int $idReset): void { $this->idReset = $idReset; }
    public function getIdUtente(): int { return $this->idUtente; }
    public function setIdUtente(int $idUtente): void { $this->idUtente = $idUtente; }
    public function getToken(): string { return $this->token; }
    public function setToken(string $token): void { $this->token = $token; }
    public function getScadenza(): string { return $this->scadenza; }
    public function setScadenza(string $scadenza): void { $this->scadenza = $scadenza; }
    public function getUsato(): bool { return $this->usato; }
    public function isUsato(): bool { return $this->usato; }
    public function setUsato(bool $usato): void { $this->usato = $usato; }
    public function getCreatoIl(): ?string { return $this->creatoIl; }
    public function setCreatoIl(?string $creatoIl): void { $this->creatoIl = $creatoIl; }

    /** Marca il token come già utilizzato, impedendo un secondo reset con lo stesso link. */
    public function segnaUsato(): void
    {
        $this->usato = true;
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_reset'  => $this->idReset,
            'id_utente' => $this->idUtente,
            'token'     => $this->token,
            'scadenza'  => $this->scadenza,
            'usato'     => self::boolToDb($this->usato),
            'creato_il' => $this->creatoIl,
        ]);
    }

    public function __toString(): string
    {
        return 'Password reset #' . ($this->idReset ?? 'nuovo') . ' - utente ' . $this->idUtente;
    }
}
