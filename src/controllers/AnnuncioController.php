<?php

namespace App\Controllers;

use App\Entity\EAnnuncio;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use finfo;
use PDO;

/**
 * Gestisce annunci, dettaglio pubblico, creazione/modifica e immagini caricate.
 */
class AnnuncioController extends BaseController
{
    private PDO $db;

    /**
     * Mantiene PDO per transazioni e query dirette.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Lista annunci con eventuale ricerca testuale o filtro categoria.
     */
    public function lista(): void
    {
        $q = trim($_GET['q'] ?? '');
        $idCategoria = (int) ($_GET['id_categoria'] ?? 0);
        $categorie = $this->entitiesToArrays(FPersistentManager::categorie());

        if ($q !== '' || $idCategoria > 0) {
            $annunci = $this->entitiesToArrays(FPersistentManager::searchAnnunci($q, $idCategoria));
            $utenti = $this->entitiesToArrays(FPersistentManager::searchUtenti($q));
        } else {
            $annunci = $this->entitiesToArrays(FPersistentManager::annunciAttivi());
            $utenti = [];
        }

        $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);
        $wishlistIds = $isRegularUser ? FPersistentManager::wishlistIdsByUser((int) $_SESSION['user_id']) : [];
        $carrelloIds = $isRegularUser ? FPersistentManager::carrelloAnnuncioIdsByUser((int) $_SESSION['user_id']) : [];

        $this->view('annunci/lista.tpl', compact('q', 'idCategoria', 'categorie', 'annunci', 'utenti', 'wishlistIds', 'carrelloIds'), 'Annunci');
    }

    /**
     * Mostra dettagli annuncio con dati del venditore e stato wishlist/carrello dell'utente.
     */
    public function dettaglio(int $idAnnuncio): void
    {
        $annuncioEntity = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncioEntity) {
            $this->renderError('Annuncio non trovato.', 404);
            return;
        }

        $annuncio = $this->entityToArray($annuncioEntity);
        $idVenditore = (int) ($annuncioEntity->getIdUtente() ?? 0);
        $feedbackVenditore = $idVenditore > 0
            ? $this->entitiesToArrays(FPersistentManager::feedbackByVenditore($idVenditore))
            : [];
        $mediaVenditore = $idVenditore > 0 ? FPersistentManager::mediaFeedbackVenditore($idVenditore) : 0.0;

        $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);
        $wishlistIds = $isRegularUser ? FPersistentManager::wishlistIdsByUser((int) $_SESSION['user_id']) : [];
        $carrelloIds = $isRegularUser ? FPersistentManager::carrelloAnnuncioIdsByUser((int) $_SESSION['user_id']) : [];

        $this->view('annunci/dettaglio.tpl', compact('annuncio', 'feedbackVenditore', 'mediaVenditore', 'wishlistIds', 'carrelloIds'), $annuncio['titolo'] ?? 'Annuncio');
    }

    /**
     * Mostra il form vuoto per la pubblicazione di un nuovo annuncio.
     */
    public function formCreazione(): void
    {
        $categorie = $this->entitiesToArrays(FPersistentManager::categorie());

        $this->view('annunci/form.tpl', compact('categorie'), 'Nuovo annuncio');
    }

    /**
     * Mostra il form di modifica solo al proprietario di un annuncio ancora attivo.
     */
    public function formModifica(int $idAnnuncio, int $idUtente): void
    {
        try {
            $annuncioEntity = FPersistentManager::annuncioById($idAnnuncio);
            $annuncio = $this->entityToArray($annuncioEntity);

            if (!$annuncioEntity || (int)($annuncioEntity->getIdUtente() ?? 0) !== $idUtente || !$annuncioEntity->isAttivo()) {
                throw new ServiceException('Non puoi modificare questo annuncio.');
            }

            $categorie = $this->entitiesToArrays(FPersistentManager::categorie());
            $isEdit = true;

            $this->view('annunci/form.tpl', compact('categorie', 'annuncio', 'isEdit'), 'Modifica annuncio');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 403);
        }
    }

    /**
     * Riceve il POST di creazione e torna al form con errore se qualcosa non passa.
     */
    public function crea(array $data, int $idUtente, array $files = []): void
    {
        try {
            $idAnnuncio = $this->createAnnuncio($data, $idUtente, $files);

            header('Location: /annuncio/show/' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $categorie = $this->entitiesToArrays(FPersistentManager::categorie());

            $this->view('annunci/form.tpl', compact('errore', 'categorie'), 'Nuovo annuncio');
        }
    }

    /**
     * Aggiorna dati e immagini di un annuncio esistente.
     */
    public function aggiorna(array $data, int $idUtente, array $files = []): void
    {
        $idAnnuncio = (int)($data['id_annuncio'] ?? 0);

        try {
            $this->updateAnnuncio($idAnnuncio, $idUtente, $data, $files);

            header('Location: /annuncio/show/' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $categorie = $this->entitiesToArrays(FPersistentManager::categorie());
            $annuncioEntity = $idAnnuncio > 0 ? FPersistentManager::annuncioById($idAnnuncio) : null;
            $annuncio = $annuncioEntity ? $this->entityToArray($annuncioEntity) : $data;
            $isEdit = true;

            $this->view('annunci/form.tpl', compact('errore', 'categorie', 'annuncio', 'isEdit'), 'Modifica annuncio');
        }
    }

    /**
     * Elimina una singola immagine dopo aver verificato che appartenga all'utente.
     */
    public function eliminaImmagine(array $data, int $idUtente): void
    {
        try {
            $idAnnuncio = $this->deleteImage((int)($data['id_immagine'] ?? 0), $idUtente);

            header('Location: /annuncio/edit/' . $idAnnuncio);
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 403);
        }
    }

    /**
     * Elimina logicamente/fisicamente un annuncio dell'utente corrente.
     */
    public function elimina(int $idAnnuncio, int $idUtente): void
    {
        try {
            $this->requirePositiveId($idAnnuncio, 'Annuncio');
            $this->requirePositiveId($idUtente, 'Utente');

            if (!FPersistentManager::deleteAnnuncioForUser($idAnnuncio, $idUtente)) {
                throw new ServiceException('Non puoi eliminare questo annuncio.');
            }

            header('Location: /annuncio/list');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 403);
        }
    }

    /**
     * Crea annuncio e immagini nella stessa transazione per evitare dati incompleti.
     */
    private function createAnnuncio(array $data, int $idUtente, array $files = []): int
    {
        $this->requirePositiveId($idUtente, 'Utente');
        [$titolo, $descrizione, $idCategoria, $statoConservazione, $prezzo] = $this->validateAnnuncioData($data);

        $this->db->beginTransaction();

        try {
            $annuncio = EAnnuncio::fromArray([
                'id_utente' => $idUtente,
                'id_categoria' => $idCategoria,
                'titolo' => $titolo,
                'descrizione' => $descrizione !== '' ? $descrizione : null,
                'stato_conservazione' => $statoConservazione,
                'prezzo' => $prezzo,
                'modalita_consegna' => 'Consegna',
                'stato' => 'attivo',
            ]);

            $idAnnuncio = FPersistentManager::createAnnuncioForUser($annuncio, $idUtente);
            $this->saveAnnuncioImages($idAnnuncio, $files);

            $this->db->commit();

            return $idAnnuncio;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Aggiorna un annuncio solo se appartiene all'utente ed e ancora modificabile.
     */
    private function updateAnnuncio(int $idAnnuncio, int $idUtente, array $data, array $files = []): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idUtente, 'Utente');

        $annuncio = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncio || (int)($annuncio->getIdUtente() ?? 0) !== $idUtente) {
            throw new ServiceException('Non puoi modificare questo annuncio.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException('Puoi modificare solo annunci attivi.');
        }

        [$titolo, $descrizione, $idCategoria, $statoConservazione, $prezzo] = $this->validateAnnuncioData($data);

        $this->db->beginTransaction();

        try {
            $updated = EAnnuncio::fromArray(array_merge($annuncio->toArray(), [
                'id_annuncio' => $idAnnuncio,
                'id_categoria' => $idCategoria,
                'titolo' => $titolo,
                'descrizione' => $descrizione !== '' ? $descrizione : null,
                'stato_conservazione' => $statoConservazione,
                'prezzo' => $prezzo,
            ]));

            FPersistentManager::updateAnnuncioForUser($idAnnuncio, $idUtente, $updated);
            $this->saveAnnuncioImages($idAnnuncio, $files);

            $this->db->commit();
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Valida i campi comuni a creazione e modifica annuncio.
     */
    private function validateAnnuncioData(array $data): array
    {
        $titolo = $this->clean($data['titolo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');
        $idCategoria = (int) ($data['id_categoria'] ?? 0);
        $statoConservazione = $this->clean($data['stato_conservazione'] ?? '');
        $prezzo = (float) ($data['prezzo'] ?? 0);
        $validi = ['Nuovo', 'Usato come nuovo', 'Ottimo', 'Buono', 'Discreto', 'Scarso'];

        if ($titolo === '' || $idCategoria <= 0 || $statoConservazione === '' || $prezzo <= 0) {
            throw new ServiceException("Compila tutti i campi obbligatori dell'annuncio.");
        }

        if (!in_array($statoConservazione, $validi, true)) {
            throw new ServiceException('Stato di conservazione non valido.');
        }

        return [$titolo, $descrizione, $idCategoria, $statoConservazione, $prezzo];
    }

    /**
     * Rimuove il record immagine e poi il file pubblico collegato.
     */
    private function deleteImage(int $idImmagine, int $idUtente): int
    {
        $this->requirePositiveId($idImmagine, 'Immagine');
        $this->requirePositiveId($idUtente, 'Utente');

        $immagine = FPersistentManager::findImmagineOwnedByUser($idImmagine, $idUtente);

        if (!$immagine) {
            throw new ServiceException('Non puoi rimuovere questa foto.');
        }

        if (!FPersistentManager::deleteImmagineById($idImmagine)) {
            throw new ServiceException('Foto non rimossa.');
        }

        $this->deleteImageFile($immagine->getUrl());

        return (int) $immagine->getIdAnnuncio();
    }

    /**
     * Cancella il file solo se il path resta dentro public.
     */
    private function deleteImageFile(string $url): void
    {
        $url = trim($url);

        if ($url === '' || str_contains($url, '..')) {
            return;
        }

        $path = realpath(__DIR__ . '/../../public/' . ltrim(str_replace('\\', '/', $url), '/'));
        $publicRoot = realpath(__DIR__ . '/../../public');

        if ($path && $publicRoot && str_starts_with($path, $publicRoot) && is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Salva fino a 5 immagini per annuncio, validando dimensione e MIME reali.
     */
    private function saveAnnuncioImages(int $idAnnuncio, array $files): void
    {
        if (empty($files['immagini']) || empty($files['immagini']['name'])) {
            return;
        }

        $immagini = $files['immagini'];
        $nomi = is_array($immagini['name']) ? $immagini['name'] : [$immagini['name']];
        $tmpNames = is_array($immagini['tmp_name']) ? $immagini['tmp_name'] : [$immagini['tmp_name']];
        $errori = is_array($immagini['error']) ? $immagini['error'] : [$immagini['error']];
        $dimensioni = is_array($immagini['size']) ? $immagini['size'] : [$immagini['size']];
        $maxFile = 5;
        $maxSize = 3 * 1024 * 1024;
        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $uploadDir = __DIR__ . '/../../public/uploads/annunci/' . $idAnnuncio;
        $publicDir = '/uploads/annunci/' . $idAnnuncio;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            throw new ServiceException('Impossibile creare la cartella per le immagini.');
        }

        $existing = FPersistentManager::countImmaginiByAnnuncio($idAnnuncio);

        if ($existing >= $maxFile) {
            return;
        }

        $ordine = $existing;
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($nomi as $index => $nomeOriginale) {
            if ($ordine >= $maxFile) {
                break;
            }

            $errore = $errori[$index] ?? UPLOAD_ERR_NO_FILE;

            if ($errore === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($errore !== UPLOAD_ERR_OK) {
                throw new ServiceException('Errore durante il caricamento di una foto.');
            }

            $tmpName = $tmpNames[$index] ?? '';
            $size = (int) ($dimensioni[$index] ?? 0);

            if ($size <= 0 || $size > $maxSize) {
                throw new ServiceException('Ogni foto deve pesare al massimo 3 MB.');
            }

            $mime = $finfo->file($tmpName);

            if (!isset($allowedMime[$mime])) {
                throw new ServiceException('Puoi caricare solo immagini JPG, PNG o WEBP.');
            }

            $filename = bin2hex(random_bytes(16)) . '.' . $allowedMime[$mime];
            $destination = $uploadDir . '/' . $filename;

            if (!move_uploaded_file($tmpName, $destination)) {
                throw new ServiceException('Impossibile salvare una foto caricata.');
            }

            FPersistentManager::addImmagineForAnnuncio($idAnnuncio, $publicDir . '/' . $filename, $ordine);
            $ordine++;
        }
    }
}
