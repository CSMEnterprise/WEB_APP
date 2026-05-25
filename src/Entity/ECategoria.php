<?php

namespace App\Entity;

class ECategoria extends EBaseEntity
{
    private $idCategoria;
    private $nome;
    private $idPadre;
    private $figli;

    public function __construct(string $nome = '', ?int $idPadre = null)
    {
        $this->nome = $nome;
        $this->idPadre = $idPadre;
        $this->figli = [];
    }

    public static function fromArray(array $data): self
    {
        $categoria = new self(
            (string) self::read($data, 'nome', 'nome', ''),
            self::intOrNull(self::read($data, 'id_padre', 'idPadre'))
        );
        $categoria->setIdCategoria(self::intOrNull(self::read($data, 'id_categoria', 'idCategoria')));
        $categoria->rememberExtra($data, array_keys($categoria->toArray()));

        return $categoria;
    }

    public function getIdCategoria(): ?int
    {
        return $this->idCategoria;
    }

    public function setIdCategoria(?int $idCategoria): void
    {
        $this->idCategoria = $idCategoria;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getIdPadre(): ?int
    {
        return $this->idPadre;
    }

    public function setIdPadre(?int $idPadre): void
    {
        $this->idPadre = $idPadre;
    }

    public function getFigli(): array
    {
        return $this->figli;
    }

    public function addFiglio(ECategoria $categoria): void
    {
        $this->figli[] = $categoria;
    }

    public function removeFiglio(int $pos): void
    {
        unset($this->figli[$pos]);
        $this->figli = array_values($this->figli);
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_categoria' => $this->idCategoria,
            'nome' => $this->nome,
            'id_padre' => $this->idPadre,
            'figli' => array_map(static fn($figlio) => $figlio instanceof ECategoria ? $figlio->toArray() : $figlio, $this->figli),
        ]);
    }

    public function __toString(): string
    {
        return 'Categoria #' . ($this->idCategoria ?? 'nuova') . ' - ' . $this->nome;
    }
}
