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
}
