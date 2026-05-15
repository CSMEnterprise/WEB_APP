<?php

require_once __DIR__ . '/BaseService.php';

class UserService extends BaseService
{
    public function findById(int $idUtente): ?array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT id_utente, email, username, nome, telefono, indirizzo, propic, stato_ban, data_registrazione
            FROM utente_registrato
            WHERE id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id_utente, email, username, nome, telefono, indirizzo, stato_ban, data_registrazione
            FROM utente_registrato
            ORDER BY data_registrazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function banna(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET stato_ban = 1
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
    }

    public function sblocca(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET stato_ban = 0
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
    }

    public function updateIndirizzoSpedizione(int $idUtente, array $data): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nome = $this->clean($data['nome'] ?? '');
        $indirizzo = $this->clean($data['indirizzo'] ?? '');

        if ($nome === '' || $indirizzo === '') {
            throw new ServiceException('Nome, cognome e indirizzo di spedizione sono obbligatori.');
        }

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET nome = ?,
                indirizzo = ?
            WHERE id_utente = ?
        ");

        $stmt->execute([
            $nome,
            $indirizzo,
            $idUtente
        ]);
    }
}
