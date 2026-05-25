<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

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

    public function getAllAdminsEntity(): array
    {
        return array_map(static fn(array $admin) => EAdmin::fromArray($admin), $this->getAllAdmins());
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

    public function getAzioniByAdminEntity(int $idAdmin): array
    {
        return $this->toModeraEntities($this->getAzioniByAdmin($idAdmin));
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

    public function getAzioniModerazioneEntity(array $filters = []): array
    {
        return $this->toModeraEntities($this->getAzioniModerazione($filters));
    }

    public function registraAzione(
        int $idAdmin,
        string $azione,
        ?int $idUtente = null,
        ?int $idFeedback = null,
        ?int $idAnnuncio = null,
        ?int $idBusiness = null
    ): void {
        $moderazione = new EModera($idAdmin, $azione);
        $moderazione->setIdUtente($idUtente);
        $moderazione->setIdFeedback($idFeedback);
        $moderazione->setIdAnnuncio($idAnnuncio);
        $moderazione->setIdBusiness($idBusiness);

        $this->registraAzioneDaEntity($moderazione);
    }

    public function registraAzioneDaEntity(EModera $moderazione): void
    {
        $idAdmin = $moderazione->getIdAdmin();
        $azione = $this->clean($moderazione->getAzioneCompiuta());

        $this->requirePositiveId($idAdmin, 'Admin');

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
            $moderazione->getIdUtente(),
            $moderazione->getIdFeedback(),
            $moderazione->getIdAnnuncio(),
            $moderazione->getIdBusiness(),
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

    private function findAdminForModeration(int $idAdmin): EAdmin
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

        return EAdmin::fromArray($admin);
    }

    private function ensureCanModerateAdmin(EAdmin $target, EAdmin $current): void
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

    private function toModeraEntities(array $azioni): array
    {
        return array_map(static fn(array $azione) => EModera::fromArray($azione), $azioni);
    }
}
