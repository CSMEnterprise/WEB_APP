<?php

require_once __DIR__ . '/BaseService.php';

class BusinessService extends BaseService
{
    public function create(int $userId, array $data): int
    {
        $this->requirePositiveInt($userId, 'id_utente');
        $this->validate($data);

        $this->execute(
            'INSERT INTO account_business
                (id_utente, p_iva, nome_azienda, logo, descrizione, indirizzo, telefono, email_aziendale, link_social)
             VALUES
                (:id_utente, :p_iva, :nome_azienda, :logo, :descrizione, :indirizzo, :telefono, :email_aziendale, :link_social)',
            [
                ':id_utente' => $userId,
                ':p_iva' => trim($data['p_iva']),
                ':nome_azienda' => trim($data['nome_azienda']),
                ':logo' => trim($data['logo'] ?? '') ?: null,
                ':descrizione' => trim($data['descrizione'] ?? '') ?: null,
                ':indirizzo' => trim($data['indirizzo'] ?? '') ?: null,
                ':telefono' => trim($data['telefono'] ?? '') ?: null,
                ':email_aziendale' => strtolower(trim($data['email_aziendale'])),
                ':link_social' => trim($data['link_social'] ?? '') ?: null,
            ]
        );

        return $this->lastInsertId();
    }

    public function update(int $businessId, array $data): bool
    {
        $this->requirePositiveInt($businessId, 'id_acc_business');
        $this->validate($data);

        return $this->execute(
            'UPDATE account_business
             SET p_iva = :p_iva,
                 nome_azienda = :nome_azienda,
                 logo = :logo,
                 descrizione = :descrizione,
                 indirizzo = :indirizzo,
                 telefono = :telefono,
                 email_aziendale = :email_aziendale,
                 link_social = :link_social
             WHERE id_acc_business = :id',
            [
                ':p_iva' => trim($data['p_iva']),
                ':nome_azienda' => trim($data['nome_azienda']),
                ':logo' => trim($data['logo'] ?? '') ?: null,
                ':descrizione' => trim($data['descrizione'] ?? '') ?: null,
                ':indirizzo' => trim($data['indirizzo'] ?? '') ?: null,
                ':telefono' => trim($data['telefono'] ?? '') ?: null,
                ':email_aziendale' => strtolower(trim($data['email_aziendale'])),
                ':link_social' => trim($data['link_social'] ?? '') ?: null,
                ':id' => $businessId,
            ]
        );
    }

    public function findById(int $businessId): ?array
    {
        $this->requirePositiveInt($businessId, 'id_acc_business');

        return $this->fetchOne(
            'SELECT b.*, u.username, u.email
             FROM account_business b
             INNER JOIN utente_registrato u ON u.id_utente = b.id_utente
             WHERE b.id_acc_business = :id',
            [':id' => $businessId]
        );
    }

    public function findByUser(int $userId): ?array
    {
        $this->requirePositiveInt($userId, 'id_utente');

        return $this->fetchOne(
            'SELECT * FROM account_business WHERE id_utente = :id_utente LIMIT 1',
            [':id_utente' => $userId]
        );
    }

    public function verify(int $businessId, int $adminId): bool
    {
        $this->requirePositiveInt($businessId, 'id_acc_business');
        $this->requirePositiveInt($adminId, 'id_admin');

        return $this->execute(
            'UPDATE account_business
             SET verificato = 1,
                 id_admin_verifica = :id_admin,
                 data_verifica = CURRENT_TIMESTAMP
             WHERE id_acc_business = :id',
            [
                ':id_admin' => $adminId,
                ':id' => $businessId,
            ]
        );
    }

    public function pendingVerification(): array
    {
        return $this->fetchAll(
            'SELECT b.*, u.username, u.email
             FROM account_business b
             INNER JOIN utente_registrato u ON u.id_utente = b.id_utente
             WHERE b.verificato = 0
             ORDER BY b.data_registrazione DESC'
        );
    }

    private function validate(array $data): void
    {
        $this->requireNotEmpty($data['p_iva'] ?? '', 'partita IVA');
        $this->requireNotEmpty($data['nome_azienda'] ?? '', 'nome azienda');
        $this->requireNotEmpty($data['email_aziendale'] ?? '', 'email aziendale');

        if (!filter_var($data['email_aziendale'], FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Email aziendale non valida.');
        }
    }
}
