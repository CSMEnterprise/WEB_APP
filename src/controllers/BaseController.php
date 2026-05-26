<?php

namespace App\Controllers;

use App\Entity\EBaseEntity;
use App\Entity\EAdmin;
use App\Entity\EModera;
use App\Foundation\FPersistentManager;
use App\Foundation\SmartyView;
use App\Services\ServiceException;

abstract class BaseController
{
    protected function entityToArray(?EBaseEntity $entity): ?array
    {
        return $entity?->toArray();
    }

    protected function entitiesToArrays(array $entities): array
    {
        return array_map(
            static fn($entity) => $entity instanceof EBaseEntity ? $entity->toArray() : (array) $entity,
            $entities
        );
    }

    protected function requirePositiveId(int $id, string $fieldName = 'ID'): void
    {
        if ($id <= 0) {
            throw new ServiceException($fieldName . ' non valido.');
        }
    }

    protected function denyBusinessBuyer(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        if (FPersistentManager::businessByUser($idUtente) !== null) {
            throw new ServiceException('Gli account business possono solo vendere: carrello, wishlist e acquisto prodotti non sono disponibili.');
        }
    }

    /**
     * Shortcut per renderizzare un template Smarty.
     * Equivalente a SmartyView::make()->render(...).
     */
    protected function view(string $template, array $data = [], string $pageTitle = 'NerdVault'): void
    {
        SmartyView::make()->render($template, $data, $pageTitle);
    }

    protected function clean(?string $value): string
    {
        return trim((string) $value);
    }

    protected function nullablePositiveInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    protected function registerAdminAction(
        int $idAdmin,
        string $azione,
        ?int $idUtente = null,
        ?int $idFeedback = null,
        ?int $idAnnuncio = null,
        ?int $idBusiness = null
    ): void {
        $this->requirePositiveId($idAdmin, 'Admin');

        $azione = $this->clean($azione);

        if ($azione === '') {
            throw new ServiceException('Azione admin non valida.');
        }

        $moderazione = new EModera($idAdmin, $azione);
        $moderazione->setIdUtente($idUtente);
        $moderazione->setIdFeedback($idFeedback);
        $moderazione->setIdAnnuncio($idAnnuncio);
        $moderazione->setIdBusiness($idBusiness);

        FPersistentManager::createModerazione($moderazione);
    }

    protected function findAdminForModeration(int $idAdmin): EAdmin
    {
        $this->requirePositiveId($idAdmin, 'Admin');

        $admin = FPersistentManager::adminForModeration($idAdmin);

        if (!$admin instanceof EAdmin) {
            throw new ServiceException('Admin non trovato.');
        }

        return $admin;
    }

    protected function ensureCanModerateAdmin(EAdmin $target, EAdmin $current): void
    {
        if ($current->getLivelloSicurezza() !== 2) {
            throw new ServiceException('Solo un admin di livello 2 puo moderare altri admin.');
        }

        if ((int) ($target->getIdAdmin() ?? 0) === (int) ($current->getIdAdmin() ?? 0)) {
            throw new ServiceException('Non puoi bannare o sbloccare il tuo account admin.');
        }

        if ($target->getLivelloSicurezza() !== 1) {
            throw new ServiceException('Puoi moderare solo admin di livello 1.');
        }
    }
}
