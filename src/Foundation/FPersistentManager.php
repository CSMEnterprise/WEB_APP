<?php

namespace App\Foundation;

use App\Entity\{
    EAccountBusiness,
    EAnnuncio,
    EBaseEntity,
    ECarrello,
    EElementoCarrello,
    EFeedback,
    EIndirizzo,
    EModera,
    EPagamento,
    EPreferito,
    ESegnalazione,
    EUtenteRegistrato
};
use RuntimeException;

/**
 * Facade statica usata dai controller per accedere al layer Foundation.
 * Nasconde quale classe tabella usare e mantiene i controller liberi da query SQL.
 */
class FPersistentManager
{
    /**
     * Salvataggio generico di una entity; gli annunci richiedono anche il proprietario.
     */
    public static function store(EBaseEntity $entity): int
    {
        $table = self::tableForEntity($entity);

        if ($entity instanceof EAnnuncio) {
            $idUtente = $entity->getIdUtente();

            if ($idUtente === null) {
                throw new RuntimeException('Annuncio senza utente proprietario.');
            }

            return $table->createForUser($entity, $idUtente);
        }

        return $table->insert($entity);
    }

    /**
     * Metodi generici usati quando basta una singola operazione CRUD su una tabella.
     */
    public static function load(string $field, mixed $value, string $foundationClass): mixed
    {
        return self::table($foundationClass)->loadByField($field, $value);
    }

    public static function exist(string $field, mixed $value, string $foundationClass): bool
    {
        return self::table($foundationClass)->existByField($field, $value);
    }

    public static function delete(string $field, mixed $value, string $foundationClass): bool
    {
        return self::table($foundationClass)->deleteByField($field, $value);
    }

    public static function update(string $field, mixed $newValue, string $pk, mixed $value, string $foundationClass): bool
    {
        return self::table($foundationClass)->updateFieldBy($field, $newValue, $pk, $value);
    }

    public static function annunciAttivi(int $idCategoria = 0): array
    {
        return self::annunci()->attivi($idCategoria);
    }

    public static function annunciCasuali(int $limit = 8, ?int $excludeUserId = null, array $excludeAnnuncioIds = []): array
    {
        return self::annunci()->casuali($limit, $excludeUserId, $excludeAnnuncioIds);
    }

    public static function annunciPerInteressiUtente(int $idUtente, int $limit = 8): array
    {
        return self::annunci()->perInteressiUtente($idUtente, $limit);
    }

    public static function annuncioById(int $idAnnuncio): ?EAnnuncio
    {
        return self::annunci()->findWithDetails($idAnnuncio);
    }

    public static function annunciByUserIdAndStato(int $idUtente, ?string $stato = 'attivo'): array
    {
        return self::annunci()->byUserIdAndStato($idUtente, $stato);
    }

    public static function createAnnuncioForUser(EAnnuncio $annuncio, int $idUtente): int
    {
        return self::annunci()->createForUser($annuncio, $idUtente);
    }

    public static function updateAnnuncioForUser(int $idAnnuncio, int $idUtente, EAnnuncio $annuncio): bool
    {
        return self::annunci()->updateForUser($idAnnuncio, $idUtente, $annuncio);
    }

    public static function deleteAnnuncioForUser(int $idAnnuncio, int $idUtente): bool
    {
        return self::annunci()->deleteForUser($idAnnuncio, $idUtente);
    }

    public static function deleteAnnuncioByAdmin(int $idAnnuncio): void
    {
        self::annunci()->deleteByAdmin($idAnnuncio);
    }

    public static function markAnnuncioSold(int $idAnnuncio): void
    {
        self::annunci()->markSold($idAnnuncio);
    }

    public static function searchAnnunci(
        string $keywords,
        int $idCategoria = 0,
        ?float $prezzoMin = null,
        ?float $prezzoMax = null,
        string $ordinamento = 'data_desc',
        ?int $limit = null,
        int $offset = 0,
        ?int $excludeUserId = null
    ): array {
        // Search rimane nel repository annunci: qui si inoltrano solo i criteri.
        return self::annunci()->search(
            $keywords,
            $idCategoria,
            $prezzoMin,
            $prezzoMax,
            $ordinamento,
            $limit,
            $offset,
            $excludeUserId
        );
    }

    public static function countSearchAnnunci(string $keywords, int $idCategoria = 0, ?float $prezzoMin = null, ?float $prezzoMax = null, ?int $excludeUserId = null): int
    {
        return self::annunci()->countSearch($keywords, $idCategoria, $prezzoMin, $prezzoMax, $excludeUserId);
    }

    public static function immaginiByAnnuncio(int $idAnnuncio): array
    {
        return self::immagini()->byAnnuncio($idAnnuncio);
    }

    public static function countImmaginiByAnnuncio(int $idAnnuncio): int
    {
        return self::immagini()->countByAnnuncio($idAnnuncio);
    }

    public static function addImmagineForAnnuncio(int $idAnnuncio, string $url, int $ordine = 0): int
    {
        return self::immagini()->addForAnnuncio($idAnnuncio, $url, $ordine);
    }

    public static function findImmagineOwnedByUser(int $idImmagine, int $idUtente): mixed
    {
        return self::immagini()->findOwnedByUser($idImmagine, $idUtente);
    }

    public static function deleteImmagineById(int $idImmagine): bool
    {
        return self::immagini()->deleteById($idImmagine);
    }

    public static function categorie(): array
    {
        return self::table(FCategoria::class)->allOrdered();
    }

    public static function utenteById(int $idUtente): ?EUtenteRegistrato
    {
        return self::utenti()->findWithDefaultAddress($idUtente);
    }

    public static function updateProfiloUtente(int $idUtente, string $nome, ?string $telefono): void
    {
        self::utenti()->updateProfile($idUtente, $nome, $telefono);
    }

    public static function updatePropicUtente(int $idUtente, string $url): void
    {
        self::utenti()->updatePropic($idUtente, $url);
    }

    public static function updateNomeUtente(int $idUtente, string $nome): void
    {
        self::utenti()->updateName($idUtente, $nome);
    }

    public static function searchUtenti(string $q): array
    {
        return self::utenti()->searchPublic($q);
    }

    public static function utentiForAdmin(string $q = ''): array
    {
        return self::utenti()->allForAdmin($q);
    }

    public static function setUtenteBanState(int $idUtente, bool $banned): void
    {
        self::utenti()->setBanState($idUtente, $banned);
    }

    public static function createIndirizzoForUser(EIndirizzo $indirizzo): int
    {
        return self::indirizzi()->createForUser($indirizzo);
    }

    public static function countIndirizziByUser(int $idUtente): int
    {
        return self::indirizzi()->countByUser($idUtente);
    }

    public static function indirizziByUser(int $idUtente): array
    {
        return self::indirizzi()->byUser($idUtente);
    }

    public static function indirizzoForUser(int $idIndirizzo, int $idUtente): ?EIndirizzo
    {
        return self::indirizzi()->findForUser($idIndirizzo, $idUtente);
    }

    public static function setIndirizzoPredefinito(int $idUtente, int $idIndirizzo): void
    {
        self::indirizzi()->setDefaultForUser($idUtente, $idIndirizzo);
    }

    public static function updateIndirizzoForUser(EIndirizzo $indirizzo): void
    {
        self::indirizzi()->updateForUser($indirizzo);
    }

    public static function deleteIndirizzoForUser(int $idIndirizzo, int $idUtente): void
    {
        self::indirizzi()->deleteForUser($idIndirizzo, $idUtente);
    }

    public static function makeMostRecentIndirizzoDefault(int $idUtente): void
    {
        self::indirizzi()->makeMostRecentDefault($idUtente);
    }

    public static function wishlistAnnunciByUser(int $idUtente): array
    {
        return self::preferiti()->annunciByUser($idUtente);
    }

    public static function preferitiByUser(int $idUtente): array
    {
        return self::preferiti()->preferitiByUser($idUtente);
    }

    public static function wishlistIdsByUser(int $idUtente): array
    {
        return self::preferiti()->idsByUser($idUtente);
    }

    public static function addPreferito(EPreferito $preferito): void
    {
        self::preferiti()->add($preferito);
    }

    public static function removePreferito(int $idUtente, int $idAnnuncio): void
    {
        self::preferiti()->remove($idUtente, $idAnnuncio);
    }

    public static function removePreferitiByAnnuncio(int $idAnnuncio): void
    {
        self::preferiti()->removeByAnnuncio($idAnnuncio);
    }

    public static function preferitoExists(int $idUtente, int $idAnnuncio): bool
    {
        return self::preferiti()->existsForUser($idUtente, $idAnnuncio);
    }

    public static function clearPreferitiForUser(int $idUtente): void
    {
        self::preferiti()->clearForUser($idUtente);
    }

    public static function removeUnavailablePreferitiForUser(int $idUtente): void
    {
        self::preferiti()->removeUnavailableForUser($idUtente);
    }

    public static function getOrCreateCartIdByUser(int $idUtente): int
    {
        return self::carrelli()->getOrCreateIdByUser($idUtente);
    }

    public static function carrelloById(int $idCarrello): ?ECarrello
    {
        return self::carrelli()->findCart($idCarrello);
    }

    public static function elementiCarrelloAcquistabili(int $idCarrello): array
    {
        return self::elementiCarrello()->elementiAcquistabili($idCarrello);
    }

    public static function carrelloAnnuncioIdsByUser(int $idUtente): array
    {
        return self::elementiCarrello()->activeAnnuncioIdsByUser($idUtente);
    }

    public static function addElementoCarrello(EElementoCarrello $elemento): void
    {
        self::elementiCarrello()->add($elemento);
    }

    public static function removeElementoCarrello(int $idCarrello, int $idAnnuncio): void
    {
        self::elementiCarrello()->remove($idCarrello, $idAnnuncio);
    }

    public static function removeAnnuncioFromAllCarts(int $idAnnuncio): void
    {
        self::elementiCarrello()->removeFromAllCarts($idAnnuncio);
    }

    public static function unavailableCartItems(int $idCarrello): array
    {
        return self::elementiCarrello()->unavailableByCart($idCarrello);
    }

    public static function removeUnavailableCartItems(int $idCarrello): void
    {
        self::elementiCarrello()->removeUnavailableByCart($idCarrello);
    }

    public static function clearCart(int $idCarrello): void
    {
        self::elementiCarrello()->clearCart($idCarrello);
    }

    public static function createFeedback(EFeedback $feedback): int
    {
        return self::feedback()->create($feedback);
    }

    public static function feedbackByPagamento(int $idPagamento): array
    {
        return self::feedback()->byPagamento($idPagamento);
    }

    public static function feedbackByUser(int $idUtente): array
    {
        return self::feedback()->byUser($idUtente);
    }

    public static function feedbackByVenditore(int $idVenditore): array
    {
        return self::feedback()->byVenditore($idVenditore);
    }

    public static function feedbackExists(int $idPagamento, int $idAutore): bool
    {
        return self::feedback()->existsForPagamentoAndAutore($idPagamento, $idAutore);
    }

    public static function mediaFeedbackVenditore(int $idUtente): float
    {
        return self::feedback()->averageForVenditore($idUtente);
    }

    public static function createSegnalazione(ESegnalazione $segnalazione): int
    {
        return self::segnalazioniTable()->create($segnalazione);
    }

    public static function segnalazioni(array $filters = []): array
    {
        return self::segnalazioniTable()->allWithDetails($filters);
    }

    public static function closeSegnalazione(int $idSegnalazione, int $idAdmin): void
    {
        self::segnalazioniTable()->close($idSegnalazione, $idAdmin);
    }

    public static function deleteSegnalazione(int $idSegnalazione): bool
    {
        return self::segnalazioniTable()->deleteById($idSegnalazione);
    }

    public static function admins(): array
    {
        return self::adminTable()->allOrdered();
    }

    public static function adminForModeration(int $idAdmin): mixed
    {
        return self::adminTable()->findForModeration($idAdmin);
    }

    public static function setAdminBanState(int $idAdmin, bool $banned): void
    {
        self::adminTable()->setBanState($idAdmin, $banned);
    }

    public static function moderaByAdmin(int $idAdmin): array
    {
        return self::modera()->byAdmin($idAdmin);
    }

    public static function azioniModerazione(array $filters = []): array
    {
        return self::modera()->withAdminDetails($filters);
    }

    public static function createModerazione(EModera $moderazione): int
    {
        return self::modera()->create($moderazione);
    }

    public static function createPagamento(EPagamento $pagamento): int
    {
        return self::pagamenti()->create($pagamento);
    }

    public static function cronologiaPagamentiByUser(int $idUtente): array
    {
        return self::pagamenti()->chronologyByUser($idUtente);
    }

    public static function pagamentoById(int $idPagamento): ?EPagamento
    {
        return self::pagamenti()->findWithAnnuncioTitle($idPagamento);
    }

    public static function ordiniRicevutiBySellerUser(int $idUtente): array
    {
        return self::pagamenti()->receivedBySellerUser($idUtente);
    }

    public static function dashboardStats(): array
    {
        $db = FDataBase::getInstance();

        // Numeri sintetici mostrati nella dashboard admin.
        return [
            'totUtenti' => $db->count('utente_registrato'),
            'totAnnunci' => $db->count('annuncio'),
            'totSegnalazioni' => $db->count('segnalazione', "`stato` IN ('Aperta','In_revisione')"),
            'totPagamenti' => $db->count('pagamento'),
        ];
    }

    public static function businessByUser(int $idUtente): ?EAccountBusiness
    {
        return self::business()->findByUserWithAddress($idUtente);
    }

    public static function createBusiness(EAccountBusiness $business): int
    {
        return self::business()->create($business);
    }

    public static function createIndirizzoForBusiness(EIndirizzo $indirizzo): int
    {
        return self::indirizzi()->createForBusiness($indirizzo);
    }

    public static function deleteDefaultBusinessAddress(int $idBusiness): void
    {
        self::indirizzi()->deleteDefaultForBusiness($idBusiness);
    }

    private static function tableForEntity(EBaseEntity $entity): object
    {
        // Convenzione: App\Entity\EAnnuncio -> App\Foundation\FAnnuncio.
        $entityClass = $entity::class;
        $foundationClass = __NAMESPACE__ . '\\F' . substr(strrchr($entityClass, '\\') ?: $entityClass, 2);

        return self::table($foundationClass);
    }

    private static function table(string $foundationClass): object
    {
        // Accetta sia class name completo sia nome breve dentro App\Foundation.
        if (!str_contains($foundationClass, '\\')) {
            $foundationClass = __NAMESPACE__ . '\\' . $foundationClass;
        }

        return FDataBase::getInstance()->table($foundationClass);
    }

    private static function annunci(): FAnnuncio
    {
        // Helper tipizzati: migliorano autocompletamento e tengono compatto il facade.
        return self::table(FAnnuncio::class);
    }

    private static function immagini(): FImmagine
    {
        return self::table(FImmagine::class);
    }

    private static function utenti(): FUtenteRegistrato
    {
        return self::table(FUtenteRegistrato::class);
    }

    private static function indirizzi(): FIndirizzo
    {
        return self::table(FIndirizzo::class);
    }

    private static function preferiti(): FPreferito
    {
        return self::table(FPreferito::class);
    }

    private static function carrelli(): FCarrello
    {
        return self::table(FCarrello::class);
    }

    private static function elementiCarrello(): FElementoCarrello
    {
        return self::table(FElementoCarrello::class);
    }

    private static function feedback(): FFeedback
    {
        return self::table(FFeedback::class);
    }

    private static function segnalazioniTable(): FSegnalazione
    {
        return self::table(FSegnalazione::class);
    }

    private static function adminTable(): FAdmin
    {
        return self::table(FAdmin::class);
    }

    private static function modera(): FModera
    {
        return self::table(FModera::class);
    }

    private static function pagamenti(): FPagamento
    {
        return self::table(FPagamento::class);
    }

    private static function business(): FAccountBusiness
    {
        return self::table(FAccountBusiness::class);
    }
}
