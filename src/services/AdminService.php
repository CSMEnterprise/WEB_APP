<?php

require_once __DIR__ . '/BaseService.php';

class AdminService extends BaseService
{
    public function getDashboardStats(): array
    {
        return [
            'totUtenti' => (int) $this->db->query("SELECT COUNT(*) FROM utente_registrato")->fetchColumn(),
            'totAnnunci' => (int) $this->db->query("SELECT COUNT(*) FROM annuncio")->fetchColumn(),
            'totSegnalazioni' => (int) $this->db->query("SELECT COUNT(*) FROM segnalazione WHERE stato IN ('Aperta','In_revisione')")->fetchColumn(),
            'totPagamenti' => (int) $this->db->query("SELECT COUNT(*) FROM pagamento")->fetchColumn(),
        ];
    }

    public function getAllAdmins(): array
    {
        $stmt = $this->db->query("
            SELECT id_admin, email, livello_sicurezza, stato_ban, data_creazione
            FROM admin
            ORDER BY livello_sicurezza DESC, data_creazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function getAzioniByAdmin(int $idAdmin): array
    {
        $this->requirePositiveId($idAdmin, 'Admin');

        $stmt = $this->db->prepare("
            SELECT *
            FROM modera
            WHERE id_admin = ?
            ORDER BY data_azione DESC, id_moderazione DESC
        ");
        $stmt->execute([$idAdmin]);

        return $stmt->fetchAll();
    }

    public function getAzioniModerazione(array $filters = []): array
    {
        $adminSearch = $this->clean($filters['admin'] ?? '');

        $where = [];
        $params = [];

        if ($adminSearch !== '') {
            $where[] = "(a.email LIKE CONCAT('%', ?, '%') OR m.id_admin = ?)";
            $params[] = $adminSearch;
            $params[] = ctype_digit($adminSearch) ? (int) $adminSearch : 0;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT
                m.*,
                a.email AS admin_email,
                a.livello_sicurezza
            FROM modera m
            JOIN admin a ON a.id_admin = m.id_admin
            {$whereSql}
            ORDER BY m.data_azione DESC, m.id_moderazione DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function registraAzione(
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

        $stmt = $this->db->prepare("
            INSERT INTO modera
            (id_admin, id_utente, id_feedback, id_annuncio, id_business, azione_compiuta)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $idAdmin,
            $idUtente,
            $idFeedback,
            $idAnnuncio,
            $idBusiness,
            $azione,
        ]);
    }

    public function bannaAdminLivello1(int $idAdminDaBannare, int $idAdminCorrente): void
    {
        $target = $this->findAdminForModeration($idAdminDaBannare);
        $current = $this->findAdminForModeration($idAdminCorrente);

        $this->ensureCanModerateAdmin($target, $current);

        $stmt = $this->db->prepare("
            UPDATE admin
            SET stato_ban = 1
            WHERE id_admin = ?
        ");
        $stmt->execute([$idAdminDaBannare]);
    }

    public function sbloccaAdminLivello1(int $idAdminDaSbloccare, int $idAdminCorrente): void
    {
        $target = $this->findAdminForModeration($idAdminDaSbloccare);
        $current = $this->findAdminForModeration($idAdminCorrente);

        $this->ensureCanModerateAdmin($target, $current);

        $stmt = $this->db->prepare("
            UPDATE admin
            SET stato_ban = 0
            WHERE id_admin = ?
        ");
        $stmt->execute([$idAdminDaSbloccare]);
    }

    private function findAdminForModeration(int $idAdmin): array
    {
        $this->requirePositiveId($idAdmin, 'Admin');

        $stmt = $this->db->prepare("
            SELECT id_admin, email, livello_sicurezza, stato_ban
            FROM admin
            WHERE id_admin = ?
            LIMIT 1
        ");
        $stmt->execute([$idAdmin]);

        $admin = $stmt->fetch();

        if (!$admin) {
            throw new ServiceException('Admin non trovato.');
        }

        return $admin;
    }

    private function ensureCanModerateAdmin(array $target, array $current): void
    {
        if ((int) ($current['livello_sicurezza'] ?? 1) !== 2) {
            throw new ServiceException('Solo un admin di livello 2 puo moderare altri admin.');
        }

        if ((int) ($target['id_admin'] ?? 0) === (int) ($current['id_admin'] ?? 0)) {
            throw new ServiceException('Non puoi bannare o sbloccare il tuo account admin.');
        }

        if ((int) ($target['livello_sicurezza'] ?? 1) !== 1) {
            throw new ServiceException('Puoi moderare solo admin di livello 1.');
        }
    }
}
