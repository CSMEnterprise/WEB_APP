<?php

namespace App\Entity;

/**
 * Rappresenta una categoria di annunci (struttura ad albero).
 *
 * Corrisponde alla tabella `categoria`.
 * Le categorie sono organizzate in gerarchia padre-figlio tramite idPadre:
 * - se idPadre è null, la categoria è di primo livello (es. "Manga", "TCG Cards")
 * - altrimenti è una sottocategoria (es. "Shonen" sotto "Manga")
 *
 * La lista $figli non viene persistita direttamente su DB, ma è popolata
 * lato applicazione per costruire l'albero di navigazione.
 */
class ECategoria extends EBaseEntity
{
    private $idCategoria;
    private $nome;
    /** ID della categoria padre, null se categoria radice */
    private $idPadre;
    /** @var ECategoria[] Sottocategorie figlie, popolate lato applicazione (non su DB) */
    private $figli;

    public function __construct(string $nome = '', ?int $idPadre = null)
    {
        $this->nome = $nome;
        $this->idPadre = $idPadre;
        $this->figli = [];
    }

    /** Costruisce l'entity da un array associativo (riga DB o payload form). */
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

    /** Aggiunge una sottocategoria figlia. */
    public function addFiglio(ECategoria $categoria): void
    {
        $this->figli[] = $categoria;
    }

    /** Rimuove la sottocategoria alla posizione $pos e ricompatta l'array. */
    public function removeFiglio(int $pos): void
    {
        unset($this->figli[$pos]);
        $this->figli = array_values($this->figli);
    }

    public function toArray(): array
    {
        return $this->withExtra([
            'id_categoria' => $this->idCategoria,
            'nome'         => $this->nome,
            'id_padre'     => $this->idPadre,
            'figli'        => array_map(
                static fn($figlio) => $figlio instanceof ECategoria ? $figlio->toArray() : $figlio,
                $this->figli
            ),
        ]);
    }

    public function __toString(): string
    {
        return 'Categoria #' . ($this->idCategoria ?? 'nuova') . ' - ' . $this->nome;
    }
}
