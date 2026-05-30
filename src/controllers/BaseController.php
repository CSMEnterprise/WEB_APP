<?php

namespace App\Controllers;

use App\Entity\{
    EAdmin,
    EBaseEntity,
    EModera
};
use App\Foundation\{
    FPersistentManager
};
use App\Services\ServiceException;
use App\View\SmartyView;
use PDO;

/**
 * Classe base condivisa da tutti i controller.
 * Contiene helper comuni per view, validazioni semplici, conversione entity/array
 * e registrazione delle azioni di moderazione.
 */
abstract class BaseController
{
    public function __construct(?PDO $db = null)
    {
    }

    /**
     * Converte una singola entity in array per passarla facilmente ai template.
     */
    protected function entityToArray(?EBaseEntity $entity): ?array
    {
        return $entity ? $this->normalizePublicPaths($entity->toArray()) : null;
    }

    /**
     * Normalizza liste di entity o array misti nel formato atteso dalle view.
     */
    protected function entitiesToArrays(array $entities): array
    {
        return array_map(
            fn($entity) => $this->normalizePublicPaths($entity instanceof EBaseEntity ? $entity->toArray() : (array) $entity),
            $entities
        );
    }

    /**
     * Rende assoluti i path pubblici salvati nel DB, cosi funzionano anche con URL profondi.
     */
    protected function normalizePublicPaths(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizePublicPaths($value);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            if (in_array($key, ['url', 'propic', 'immagine_principale'], true)) {
                $data[$key] = $this->publicPath($value);
            }
        }

        return $data;
    }

    protected function publicPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === ''
            || str_starts_with($path, '/')
            || preg_match('#^(https?:)?//#i', $path)
            || str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'assets/')) {
            return '/' . $path;
        }

        return $path;
    }

    /**
     * Verifica che un identificativo ricevuto da route/form sia valido.
     */
    protected function requirePositiveId(int $id, string $fieldName = 'ID'): void
    {
        if ($id <= 0) {
            throw new ServiceException($fieldName . ' non valido.');
        }
    }

    /**
     * Blocca gli account business dalle funzionalita da acquirente.
     */
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

    protected function renderError(string $message = 'Richiesta non valida.', int $statusCode = 400): void
    {
        http_response_code($statusCode);

        // Usa template diversi per 404 e per gli altri errori gestiti.
        $template = $statusCode === 404 ? 'errors/404.tpl' : 'errors/400.tpl';
        $title = $statusCode === 404 ? 'Pagina non trovata' : 'Errore';

        $this->view($template, ['errore' => $message], $title);
    }

    /**
     * pulizia  minima usata prima di validazioni e salvataggi.
     */
    protected function clean(?string $value): string
    {
        return trim((string) $value);
    }

    /**
     * Converte valori opzionali da form in interi positivi o null.
     */
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
        // Ogni moderazione viene tracciata per audit nella tabella dedicata.
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

    /**
     * Carica un admin e fallisce con messaggio applicativo se non esiste.
     */
    protected function findAdminForModeration(int $idAdmin): EAdmin
    {
        $this->requirePositiveId($idAdmin, 'Admin');

        $admin = FPersistentManager::adminForModeration($idAdmin);

        if (!$admin instanceof EAdmin) {
            throw new ServiceException('Admin non trovato.');
        }

        return $admin;
    }

    /**
     * Regole centralizzate per evitare che admin non autorizzati moderino altri admin.
     */
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
