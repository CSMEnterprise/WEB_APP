<?php

namespace App\Controllers;

use App\Core\Request;
use App\Entity\ESegnalazione;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;

/**
 * Gestisce segnalazioni create dagli utenti e lavorate dagli admin.
 */
class SegnalazioneController extends BaseController
{
    /**
     * Mostra il form di apertura segnalazione.
     */
    public function form(int $idAnnuncio = 0): void
    {
        $get = Request::get();
        if ($idAnnuncio > 0) {
            $get['id_annuncio'] = $idAnnuncio;
        }

        $this->view('segnalazioni/form.tpl', ['get' => $get], 'Nuova segnalazione');
    }

    /**
     * Crea una segnalazione e torna al form se i dati non sono coerenti.
     */
    public function crea(array $data, int $idSegnalante): void
    {
        try {
            $this->createSegnalazione($data, $idSegnalante);

            header('Location: /annuncio/list');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $this->view('segnalazioni/form.tpl', compact('errore'), 'Nuova segnalazione');
        }
    }

    /**
     * Lista semplice delle segnalazioni, usata dalle pagine amministrative.
     */
    public function lista(): void
    {
        $segnalazioni = FPersistentManager::segnalazioni();

        $this->view('segnalazioni/lista.tpl', compact('segnalazioni'), 'Segnalazioni');
    }

    /**
     * Chiude una segnalazione e registra l'azione dell'admin.
     */
    public function chiudi(int $idSegnalazione, int $idAdmin): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');
        $this->requirePositiveId($idAdmin, 'Admin');

        FPersistentManager::closeSegnalazione($idSegnalazione, $idAdmin);
        $this->registerAdminAction($idAdmin, 'Segnalazione chiusa #' . $idSegnalazione);

        header('Location: /admin/segnalazioni');
        exit;
    }

    /**
     * Elimina una segnalazione e registra l'azione dell'admin.
     */
    public function elimina(int $idSegnalazione, int $idAdmin): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');
        $this->requirePositiveId($idAdmin, 'Admin');

        $this->registerAdminAction($idAdmin, 'Segnalazione eliminata #' . $idSegnalazione);
        FPersistentManager::deleteSegnalazione($idSegnalazione);

        header('Location: /admin/segnalazioni');
        exit;
    }

    /**
     * Valida che la segnalazione abbia una sola destinazione tra annuncio, utente,
     * business o feedback, poi la salva come aperta.
     */
    private function createSegnalazione(array $data, int $idSegnalante): int
    {
        $segnalazione = ESegnalazione::fromArray(array_merge($data, [
            'id_segnalante' => $idSegnalante,
            'id_annuncio' => $this->nullablePositiveInt($data['id_annuncio'] ?? null),
            'id_utente_segnalato' => $this->nullablePositiveInt($data['id_utente_segnalato'] ?? null),
            'id_business' => $this->nullablePositiveInt($data['id_business'] ?? null),
            'id_feedback' => $this->nullablePositiveInt($data['id_feedback'] ?? null),
        ]));

        $this->requirePositiveId($segnalazione->getIdSegnalante(), 'Segnalante');

        $tipologia = $this->clean($segnalazione->getTipologia());
        $descrizione = $this->clean($segnalazione->getDescrizione());
        $idAnnuncio = $this->nullablePositiveInt($segnalazione->getIdAnnuncio());
        $idUtenteSegnalato = $this->nullablePositiveInt($segnalazione->getIdUtenteSegnalato());
        $idBusiness = $this->nullablePositiveInt($segnalazione->getIdBusiness());
        $idFeedback = $this->nullablePositiveInt($segnalazione->getIdFeedback());

        $targets = array_filter(
            [$idAnnuncio, $idUtenteSegnalato, $idBusiness, $idFeedback],
            static fn($value) => $value !== null
        );

        // Una segnalazione deve essere precisa: una tipologia valida e un solo oggetto.
        if ($tipologia === '' || !in_array($tipologia, ['Spam', 'Truffa', 'Contenuto_inappropriato', 'Altro'], true)) {
            throw new ServiceException('Tipologia segnalazione non valida.');
        }

        if (count($targets) !== 1) {
            throw new ServiceException('Devi segnalare esattamente un elemento.');
        }

        $segnalazione->setIdAnnuncio($idAnnuncio);
        $segnalazione->setIdUtenteSegnalato($idUtenteSegnalato);
        $segnalazione->setIdBusiness($idBusiness);
        $segnalazione->setIdFeedback($idFeedback);
        $segnalazione->setTipologia($tipologia);
        $segnalazione->setDescrizione($descrizione !== '' ? $descrizione : null);
        $segnalazione->setStato('Aperta');

        return FPersistentManager::createSegnalazione($segnalazione);
    }
}
