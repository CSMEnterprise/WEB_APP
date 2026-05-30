<?php

namespace App\View;

use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use RuntimeException;
use Smarty\Smarty;

/**
 * Wrapper di Smarty usato dai controller per renderizzare i template.
 * Configura directory, plugin e variabili globali disponibili in ogni pagina.
 */
class SmartyView
{
    private Smarty $smarty;

    /**
     * Prepara Smarty e registra modifier utili nei template .tpl.
     */
    public function __construct()
    {
        $root          = dirname(__DIR__, 2);
        $templatesPath = $root . '/templates';
        $compilePath   = $root . '/templates_c';
        $cachePath     = $root . '/cache';

        $this->ensureDirectory($compilePath);
        $this->ensureDirectory($cachePath);

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($templatesPath);
        $this->smarty->setCompileDir($compilePath);
        $this->smarty->setCacheDir($cachePath);
        $this->smarty->setCaching(false);
        $this->smarty->setCompileCheck(Smarty::COMPILECHECK_ON);
        $this->smarty->setCompileId($this->smartyCompileId($root));
        $this->smarty->setForceCompile($this->envFlag('SMARTY_FORCE_COMPILE'));
        $this->smarty->setEscapeHtml(true);

        // ── Modificatori PHP utili nei template ──────────────────────────
        $this->smarty->registerPlugin('modifier', 'number_format', 'number_format');
        $this->smarty->registerPlugin('modifier', 'ucfirst', 'ucfirst');
        $this->smarty->registerPlugin('modifier', 'urlencode', 'urlencode');
        $this->smarty->registerPlugin('modifier', 'strtolower', 'strtolower');
        $this->smarty->registerPlugin('modifier', 'strtoupper', 'strtoupper');
        $this->smarty->registerPlugin('modifier', 'substr',
            static fn($value, int $start, ?int $length = null): string => $length === null
                ? substr((string) $value, $start)
                : substr((string) $value, $start, $length)
        );
        $this->smarty->registerPlugin('modifier', 'round', 'round');
        $this->smarty->registerPlugin('modifier', 'count_items',
            static fn($value): int => is_countable($value) ? count($value) : 0
        );
        $this->smarty->registerPlugin('modifier', 'in_array',
            static fn($needle, $haystack): bool => in_array((int) $needle, array_map('intval', (array) $haystack), true)
        );
        $this->smarty->registerPlugin('modifier', 'date_it',
            static fn($value): string => $value ? date('d/m/Y', strtotime((string) $value)) : ''
        );
        $this->smarty->registerPlugin('modifier', 'preg_replace_slug',
            fn($s) => strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)$s))
        );
        // nl2br_e: escape poi nl2br, output safe senza double-escape
        $this->smarty->registerPlugin('modifier', 'nl2br_e', function ($str): string {
            return nl2br(htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8'));
        });
        // stella(n): genera n stelle piene + (5-n) vuote
        $this->smarty->registerPlugin('modifier', 'star_full',
            fn($n) => str_repeat('★', max(0, (int)$n))
        );
        $this->smarty->registerPlugin('modifier', 'star_empty',
            fn($n) => str_repeat('☆', max(0, 5 - (int)$n))
        );
    }

    public static function make(): self
    {
        // Factory breve usata da BaseController::view().
        return new self();
    }

    /**
     * Assegna variabili globali (sessione, header, categorie, carrello),
     * poi le variabili di pagina, quindi visualizza il template.
     */
    public function render(string $template, array $data = [], string $pageTitle = 'NerdVault'): void
    {
        // ── Dati sempre disponibili ───────────────────────────────────────
        $this->smarty->assign('pageTitle',  $pageTitle);
        $this->smarty->assign('year',       (int) date('Y'));
        $this->smarty->assign('today',      date('d/m/Y'));
        $this->smarty->assign('get',        $_GET  ?? []);
        $this->smarty->assign('post',       $_POST ?? []);
        $this->smarty->assign('session',    $_SESSION ?? []);

        // ── Sessione ──────────────────────────────────────────────────────
        $isLogged    = !empty($_SESSION['user_id']);
        $isAdmin     = !empty($_SESSION['is_admin']);
        $isBusiness  = !empty($_SESSION['is_business']);
        $userId      = (int)($_SESSION['user_id']          ?? 0);
        $livello     = (int)($_SESSION['livello_sicurezza'] ?? 1);

        $this->smarty->assign('isLogged',         $isLogged);
        $this->smarty->assign('isAdmin',          $isAdmin);
        $this->smarty->assign('isBusiness',       $isBusiness);
        $this->smarty->assign('userId',           $userId);
        $this->smarty->assign('username',         (string)($_SESSION['username'] ?? ''));
        $this->smarty->assign('propic',           $this->publicPath((string)($_SESSION['propic'] ?? '')));
        $this->smarty->assign('livelloSicurezza', $livello);

        // ── Categorie per l'header ────────────────────────────────────────
        $categorieHeader = [];
        $pdo = null;
        try {
            $pdo = FDataBase::getInstance()->getConnection();
            $categorieHeader = array_map(
                static fn($c) => $c->toArray(),
                FPersistentManager::categorie()
            );
        } catch (RuntimeException) {
        }
        $this->smarty->assign('categorieHeader', $categorieHeader);

        // ── Contatore carrello (solo utente normale) ──────────────────────
        $cartItemCount = 0;
        if ($isLogged && !$isAdmin && !$isBusiness && $pdo instanceof \PDO) {
            try {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*)
                    FROM carrello c
                    JOIN elemento_carrello e ON e.id_carrello = c.id_carrello
                    JOIN annuncio a ON a.id_annuncio = e.id_annuncio
                    WHERE c.id_utente = ? AND a.stato = 'attivo'
                ");
                $stmt->execute([$userId]);
                $cartItemCount = (int)$stmt->fetchColumn();
            } catch (\Throwable $ignored) {
                // Il rendering non deve fallire solo per il badge carrello.
            }
        }
        $this->smarty->assign('cartItemCount', $cartItemCount);

        // ── Variabili di pagina ───────────────────────────────────────────
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->display($template);
    }

    private function ensureDirectory(string $path): void
    {
        // Smarty deve poter scrivere template compilati e cache.
        if (!is_dir($path) && !mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException('Impossibile creare la cartella Smarty: ' . $path);
        }
    }

    private function smartyCompileId(string $root): string
    {
        $headPath = $root . '/.git/HEAD';
        if (!is_file($headPath)) {
            return 'default';
        }

        $head = trim((string) file_get_contents($headPath));
        if (str_starts_with($head, 'ref: ')) {
            $refPath = $root . '/.git/' . substr($head, 5);
            if (is_file($refPath)) {
                $head = trim((string) file_get_contents($refPath));
            }
        }

        return 'git_' . substr(hash('sha256', $head), 0, 12);
    }

    private function envFlag(string $name): bool
    {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function publicPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === ''
            || str_starts_with($path, '/')
            || preg_match('#^(https?:)?//#i', $path)
            || str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'assets/')) {
            return '/' . $path;
        }

        return $path;
    }
}
