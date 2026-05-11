<?php

require_once __DIR__ . '/BaseService.php';

class AdminService extends BaseService
{
    public function create(string $email, string $password, int $securityLevel = 1): int
    {
        $email = strtolower(trim($email));
        $this->requireNotEmpty($email, 'email');
        $this->requireNotEmpty($password, 'password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email admin non valida.');
        }

        if (strlen($password) < 8) {
            throw new ServiceException('La password admin deve contenere almeno 8 caratteri.');
        }

        $this->execute(
            'INSERT INTO admin (email, password_hash, livello_sicurezza)
             VALUES (:email, :password_hash, :livello_sicurezza)',
            [
                ':email' => $email,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':livello_sicurezza' => $securityLevel,
            ]
        );

        return $this->lastInsertId();
    }

    public function dashboardStats(): array
    {
        return [
            'utenti' => (int) ($this->fetchOne('SELECT COUNT(*) AS totale FROM utente_registrato')['totale'] ?? 0),
            'annunci_attivi' => (int) ($this->fetchOne("SELECT COUNT(*) AS totale FROM annuncio WHERE stato = 'attivo'")['totale'] ?? 0),
            'annunci_venduti' => (int) ($this->fetchOne("SELECT COUNT(*) AS totale FROM annuncio WHERE stato = 'venduto'")['totale'] ?? 0),
            'segnalazioni_aperte' => (int) ($this->fetchOne("SELECT COUNT(*) AS totale FROM segnalazione WHERE stato = 'Aperta'")['totale'] ?? 0),
            'business_da_verificare' => (int) ($this->fetchOne('SELECT COUNT(*) AS totale FROM account_business WHERE verificato = 0')['totale'] ?? 0),
        ];
    }

    public function logModeration(int $adminId, string $action, array $target = []): int
    {
        $this->requirePositiveInt($adminId, 'id_admin');
        $this->requireNotEmpty($action, 'azione_compiuta');

        $this->execute(
            'INSERT INTO modera (id_admin, id_utente, id_feedback, id_annuncio, id_business, azione_compiuta)
             VALUES (:id_admin, :id_utente, :id_feedback, :id_annuncio, :id_business, :azione_compiuta)',
            [
                ':id_admin' => $adminId,
                ':id_utente' => $target['id_utente'] ?? null,
                ':id_feedback' => $target['id_feedback'] ?? null,
                ':id_annuncio' => $target['id_annuncio'] ?? null,
                ':id_business' => $target['id_business'] ?? null,
                ':azione_compiuta' => $action,
            ]
        );

        return $this->lastInsertId();
    }
}
