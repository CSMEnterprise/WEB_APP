<?php

namespace App\Controllers;

use App\Entity\{
    EAccountBusiness,
    EIndirizzo
};
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDOException;

/**
 * Gestisce area business: profilo aziendale, sede e ordini ricevuti.
 */
class BusinessController extends BaseController
{
    /**
     * Mostra profilo business e tutti gli annunci associati all'utente business
     */
    public function dashboard(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $business = $this->entityToArray(FPersistentManager::businessByUser($idUtente));
        $annunci = $this->entitiesToArrays(FPersistentManager::annunciByUserIdAndStato($idUtente, null));

        $this->view('business/profilo.tpl', compact('business', 'annunci'), 'Area business');
    }

    /**
     * Mostra il form per creare un utente in account business.
     */
    public function formCreazione(): void
    {
        $this->view('business/form.tpl', [], 'Crea account business');
    }

    /**
     * Crea l'account business e torna al form con errore in caso di dati non validi.
     */
    public function creaAccount(array $data, int $idUtente): void
    {
        try {
            $this->createBusinessAccount($data, $idUtente);

            header('Location: /business/dashboard');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $this->view('business/form.tpl', compact('errore'), 'Crea account business');
        }
    }

    /**
     * Aggiorna la sede principale dell'account business.
     */
    public function salvaIndirizzo(array $data, int $idUtente): void
    {
        $businessEntity = FPersistentManager::businessByUser($idUtente);

        if (!$businessEntity) {
            header('Location: /business/dashboard');
            exit;
        }

        try {
            $this->updateBusinessAddress((int) ($businessEntity->getIdAccBusiness() ?? 0), $data);

            header('Location: /business/dashboard');
            exit;
        } catch (Exception $e) {
            $errore = $e->getMessage();
            $business = $this->entityToArray($businessEntity);
            $annunci = $this->entitiesToArrays(FPersistentManager::annunciByUserIdAndStato($idUtente, null));

            $this->view('business/profilo.tpl', compact('errore', 'business', 'annunci'), 'Area business');
        }
    }

    /**
     * Mostra gli ordini ricevuti come venditore.
     */
    public function ordini(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $ordini = $this->entitiesToArrays(FPersistentManager::ordiniRicevutiBySellerUser($idUtente));

        $this->view('business/ordini.tpl', compact('ordini'), 'Ordini ricevuti');
    }

    /**
     * Valida dati aziendali, crea account business e opzionalmente la sede.
     */
    private function createBusinessAccount(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nomeAzienda = $this->clean($data['nome_azienda'] ?? '');
        $pIva = $this->clean($data['p_iva'] ?? $data['partita_iva'] ?? '');
        $emailAziendale = $this->clean($data['email_aziendale'] ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');
        $via = $this->clean($data['via'] ?? '');
        $numero = $this->clean($data['numero'] ?? '');
        $cap = $this->clean($data['cap'] ?? '');
        $citta = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese = $this->clean($data['paese'] ?? 'Italia');

        $this->validateBusinessData($nomeAzienda, $pIva, $emailAziendale, $telefono, $cap, $provincia, $citta);

        try {
            $idBusiness = FPersistentManager::createBusiness(EAccountBusiness::fromArray([
                'id_utente' => $idUtente,
                'p_iva' => $pIva,
                'nome_azienda' => $nomeAzienda,
                'email_aziendale' => $emailAziendale,
                'telefono' => $telefono !== '' ? $telefono : null,
            ]));

            if ($via !== '' || $citta !== '') {
                if ($via === '' || $citta === '') {
                    throw new ServiceException('Per salvare la sede aziendale devi indicare almeno via e citta.');
                }

                FPersistentManager::createIndirizzoForBusiness(EIndirizzo::fromArray([
                    'id_business' => $idBusiness,
                    'via' => $via,
                    'numero' => $numero !== '' ? $numero : null,
                    'cap' => $cap !== '' ? $cap : null,
                    'citta' => $citta,
                    'provincia' => $provincia !== '' ? $provincia : null,
                    'paese' => $paese,
                    'predefinito' => 1,
                ]));
            }

            return $idBusiness;
        } catch (PDOException $e) {
            throw new ServiceException('Account business gia esistente o dati gia utilizzati.');
        }
    }

    /**
     * Sostituisce la sede predefinita business con quella appena inviata.
     */
    private function updateBusinessAddress(int $idBusiness, array $data): void
    {
        $this->requirePositiveId($idBusiness, 'Business');

        $via = $this->clean($data['via'] ?? '');
        $numero = $this->clean($data['numero'] ?? '');
        $cap = $this->clean($data['cap'] ?? '');
        $citta = $this->clean($data['citta'] ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese = $this->clean($data['paese'] ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e citta sono obbligatori.');
        }

        FPersistentManager::deleteDefaultBusinessAddress($idBusiness);
        FPersistentManager::createIndirizzoForBusiness(EIndirizzo::fromArray([
            'id_business' => $idBusiness,
            'via' => $via,
            'numero' => $numero !== '' ? $numero : null,
            'cap' => $cap !== '' ? $cap : null,
            'citta' => $citta,
            'provincia' => $provincia !== '' ? $provincia : null,
            'paese' => $paese,
            'predefinito' => 1,
        ]));
    }

    /**
     * Regole minime per dati aziendali italiani usati in registrazione business.
     */
    private function validateBusinessData(
        string $nomeAzienda,
        string $pIva,
        string $emailAziendale,
        string $telefono,
        string $cap,
        string $provincia,
        string $citta
    ): void {
        if ($nomeAzienda === '' || $pIva === '' || $emailAziendale === '') {
            throw new ServiceException('Nome azienda, partita IVA ed email aziendale sono obbligatori.');
        }

        if (!preg_match('/^[\p{L}0-9 .&\'-]{2,80}$/u', $nomeAzienda)) {
            throw new ServiceException('Il nome azienda deve contenere 2-80 caratteri validi.');
        }

        if (!preg_match('/^[0-9]{11}$/', $pIva)) {
            throw new ServiceException('La partita IVA deve contenere esattamente 11 cifre.');
        }

        if (!filter_var($emailAziendale, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email aziendale non valida.');
        }

        if ($telefono !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e puo iniziare con +.');
        }

        if ($cap !== '' && !preg_match('/^[0-9]{5}$/', $cap)) {
            throw new ServiceException('Il CAP deve contenere esattamente 5 cifre.');
        }

        if ($provincia !== '' && !preg_match('/^[A-Za-z]{2}$/', $provincia)) {
            throw new ServiceException('La provincia deve contenere 2 lettere.');
        }

        if ($citta !== '' && !preg_match('/^[\p{L} .\'-]{2,80}$/u', $citta)) {
            throw new ServiceException('La citta deve contenere 2-80 caratteri validi.');
        }
    }
}
