<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

class CategoryService extends BaseService
{
    public function getAllEntity(): array
    {
        return $this->toCategoriaEntities($this->getAll());
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id_categoria, nome, id_padre
            FROM categoria
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll();
    }

    private function toCategoriaEntities(array $categorie): array
    {
        return array_map(static fn(array $categoria) => ECategoria::fromArray($categoria), $categorie);
    }
}
