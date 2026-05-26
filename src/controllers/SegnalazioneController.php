<?php

namespace App\Controllers;

use App\Entity\ESegnalazione;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;

class SegnalazioneController extends BaseController
{
    public function __construct(PDO $db)
    {
        FDataBase::init($db);
    }

    public function form(): void
    {
        require __DIR__ . '/../views/segnalazioni/form.php';
    }

    public function crea(array $data, int $idSegnalante): void
    {
        try {
            $this->createSegnalazione($data, $idSegnalante);

            header('Location: index.php?route=annunci');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            require __DIR__ . '/../views/segnalazioni/form.php';
        }
    }

    public function lista(): void
    {
        $segnalazioni = $this->entitiesToArrays(FPersistentManager::segnalazioni());

        require __DIR__ . '/../views/segnalazioni/lista.php';
    }

    public function chiudi(int $idSegnalazione, int $idAdmin): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');
        $this->requirePositiveId($idAdmin, 'Admin');

        FPersistentManager::closeSegnalazione($idSegnalazione, $idAdmin);
        $this->registerAdminAction($idAdmin, 'Segnalazione chiusa #' . $idSegnalazione);

        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }

    public function elimina(int $idSegnalazione, int $idAdmin): void
    {
        $this->requirePositiveId($idSegnalazione, 'Segnalazione');
        $this->requirePositiveId($idAdmin, 'Admin');

        $this->registerAdminAction($idAdmin, 'Segnalazione eliminata #' . $idSegnalazione);
        FPersistentManager::deleteSegnalazione($idSegnalazione);

        header('Location: index.php?route=admin-segnalazioni');
        exit;
    }

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
