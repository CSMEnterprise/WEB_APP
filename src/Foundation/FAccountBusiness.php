<?php

namespace App\Foundation;

use App\Entity\EAccountBusiness;

class FAccountBusiness extends FBaseTable
{
    protected function tableName(): string { return 'account_business'; }
    protected function primaryKey(): string { return 'id_acc_business'; }
    protected function entityClass(): string { return EAccountBusiness::class; }
    protected function columns(): array
    {
        return [
            'id_acc_business',
            'id_utente',
            'p_iva',
            'nome_azienda',
            'logo',
            'descrizione',
            'telefono',
            'email_aziendale',
            'link_social',
            'verificato',
            'data_registrazione',
            'id_admin_verifica',
            'data_verifica',
        ];
    }

    public function findByUserWithAddress(int $idUtente): ?EAccountBusiness
    {
        $entity = $this->fetchEntity("
            SELECT ab.*, i.`via`, i.`numero`, i.`cap`, i.`citta`, i.`provincia`, i.`paese`
            FROM `account_business` ab
            LEFT JOIN `indirizzi` i ON i.`id_business` = ab.`id_acc_business` AND i.`predefinito` = 1
            WHERE ab.`id_utente` = ?
            LIMIT 1
        ", [$idUtente]);

        return $entity instanceof EAccountBusiness ? $entity : null;
    }

    public function create(EAccountBusiness $business): int
    {
        return $this->insert([
            'id_utente' => $business->getIdUtente(),
            'p_iva' => $business->getPIva(),
            'nome_azienda' => $business->getNomeAzienda(),
            'email_aziendale' => $business->getEmailAziendale(),
            'telefono' => $business->getTelefono(),
        ]);
    }
}
