<?php

namespace App\Controllers;

use App\Foundation\FPersistentManager;
use function App\Middleware\currentUserId;

/**
 * Gestisce la home pubblica e la ricerca annunci.
 */
class HomeController extends BaseController
{
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $idCategoria = (int) ($_GET['id_categoria'] ?? 0);
        $prezzoMin = isset($_GET['prezzo_min']) && $_GET['prezzo_min'] !== '' ? max(0, (float) $_GET['prezzo_min']) : null;
        $prezzoMax = isset($_GET['prezzo_max']) && $_GET['prezzo_max'] !== '' ? max(0, (float) $_GET['prezzo_max']) : null;
        $ordinamento = (string) ($_GET['ordinamento'] ?? 'data_desc');
        $ordinamentiValidi = ['data_desc', 'data_asc', 'prezzo_asc', 'prezzo_desc'];

        if (!in_array($ordinamento, $ordinamentiValidi, true)) {
            $ordinamento = 'data_desc';
        }

        if ($prezzoMin !== null && $prezzoMax !== null && $prezzoMin > $prezzoMax) {
            [$prezzoMin, $prezzoMax] = [$prezzoMax, $prezzoMin];
        }

        $hasFiltriAvanzati = $prezzoMin !== null || $prezzoMax !== null || $ordinamento !== 'data_desc';
        $annunciPerPagina = 12;
        $paginaCorrente = max(1, (int) ($_GET['page'] ?? 1));
        $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
        $isRegularUser = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']);
        $excludeHomeUserId = $isRegularUser ? currentUserId() : null;
        $wishlistIds = [];
        $carrelloIds = [];
        $utenti = [];
        $categorie = $this->entitiesToArrays(FPersistentManager::categorie());

        if ($q !== '' || $idCategoria > 0 || $hasFiltriAvanzati) {
            $totaleAnnunci = FPersistentManager::countSearchAnnunci($q, $idCategoria, $prezzoMin, $prezzoMax, $excludeHomeUserId);
            $totalePagine = max(1, (int) ceil($totaleAnnunci / $annunciPerPagina));

            if ($paginaCorrente > $totalePagine) {
                $paginaCorrente = $totalePagine;
                $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
            }

            $homeAnnunci = $this->entitiesToArrays(FPersistentManager::searchAnnunci($q, $idCategoria, $prezzoMin, $prezzoMax, $ordinamento, $annunciPerPagina, $offsetAnnunci, $excludeHomeUserId));
            $utenti = $q !== '' ? $this->entitiesToArrays(FPersistentManager::searchUtenti($q)) : [];
            $homeTitoloAnnunci = $q !== '' ? 'Risultati per "' . $q . '"' : 'Risultati ricerca';
        } else {
            $totaleAnnunci = FPersistentManager::countSearchAnnunci('', 0, null, null, $excludeHomeUserId);
            $totalePagine = max(1, (int) ceil($totaleAnnunci / $annunciPerPagina));

            if ($paginaCorrente > $totalePagine) {
                $paginaCorrente = $totalePagine;
                $offsetAnnunci = ($paginaCorrente - 1) * $annunciPerPagina;
            }

            $homeAnnunci = $this->entitiesToArrays(FPersistentManager::searchAnnunci('', 0, null, null, 'data_desc', $annunciPerPagina, $offsetAnnunci, $excludeHomeUserId));
            $homeTitoloAnnunci = 'Annunci in evidenza';
        }

        if ($isRegularUser) {
            $wishlistIds = FPersistentManager::wishlistIdsByUser(currentUserId());
            $carrelloIds = FPersistentManager::carrelloAnnuncioIdsByUser(currentUserId());
        }

        $prezzoMinValue = $prezzoMin !== null ? (string) $prezzoMin : '';
        $prezzoMaxValue = $prezzoMax !== null ? (string) $prezzoMax : '';
        $isRicerca = $q !== '' || $idCategoria > 0 || $hasFiltriAvanzati;
        $pagination = $this->buildPagination($paginaCorrente, $totalePagine);
        $resetFiltersUrl = $this->buildResetUrl($q, $idCategoria);

        $this->view('home.tpl', compact(
            'q',
            'idCategoria',
            'prezzoMinValue',
            'prezzoMaxValue',
            'ordinamento',
            'hasFiltriAvanzati',
            'isRicerca',
            'homeAnnunci',
            'utenti',
            'categorie',
            'wishlistIds',
            'carrelloIds',
            'paginaCorrente',
            'totalePagine',
            'totaleAnnunci',
            'homeTitoloAnnunci',
            'pagination',
            'resetFiltersUrl'
        ), 'Home');
    }

    private function buildPagination(int $paginaCorrente, int $totalePagine): array
    {
        $buildPageUrl = static function (int $page): string {
            $params = $_GET;
            unset($params['route']);
            $params['page'] = $page;

            foreach ($params as $key => $value) {
                if ($value === '' || $value === null) {
                    unset($params[$key]);
                }
            }

            return '/home/index?' . http_build_query($params);
        };

        $paginaDa = max(1, $paginaCorrente - 2);
        $paginaA = min($totalePagine, $paginaCorrente + 2);
        $pages = [];

        if ($paginaDa > 1) {
            $pages[] = ['number' => 1, 'url' => $buildPageUrl(1), 'active' => false];
            if ($paginaDa > 2) {
                $pages[] = ['ellipsis' => true];
            }
        }

        for ($pagina = $paginaDa; $pagina <= $paginaA; $pagina++) {
            $pages[] = [
                'number' => $pagina,
                'url' => $buildPageUrl($pagina),
                'active' => $pagina === $paginaCorrente,
            ];
        }

        if ($paginaA < $totalePagine) {
            if ($paginaA < $totalePagine - 1) {
                $pages[] = ['ellipsis' => true];
            }

            $pages[] = ['number' => $totalePagine, 'url' => $buildPageUrl($totalePagine), 'active' => false];
        }

        return [
            'show' => $totalePagine > 1,
            'prev' => $paginaCorrente > 1 ? $buildPageUrl($paginaCorrente - 1) : '',
            'next' => $paginaCorrente < $totalePagine ? $buildPageUrl($paginaCorrente + 1) : '',
            'pages' => $pages,
        ];
    }

    private function buildResetUrl(string $q, int $idCategoria): string
    {
        $params = [];

        if ($q !== '') {
            $params['q'] = $q;
        }

        if ($idCategoria > 0) {
            $params['id_categoria'] = $idCategoria;
        }

        $query = http_build_query($params);

        return $query !== '' ? '/home/index?' . $query : '/home/index';
    }
}
