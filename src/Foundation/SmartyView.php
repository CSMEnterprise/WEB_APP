<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helpers/functions.php';

use Smarty\Smarty;

class SmartyView
{
    private Smarty $smarty;
    private string $viewsPath;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $this->viewsPath = $root . '/src/views';
        $templatesPath = $root . '/src/templates';
        $compilePath = $root . '/var/cache/smarty/compile';
        $cachePath = $root . '/var/cache/smarty/cache';

        $this->ensureDirectory($compilePath);
        $this->ensureDirectory($cachePath);

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($templatesPath);
        $this->smarty->setCompileDir($compilePath);
        $this->smarty->setCacheDir($cachePath);
        $this->smarty->setEscapeHtml(true);
    }

    public static function make(): self
    {
        return new self();
    }

    public function render(string $template, array $data = [], string $pageTitle = 'NerdVault'): void
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->assign('pageTitle', $pageTitle);

        extract($data, EXTR_SKIP);
        require $this->viewsPath . '/layout/header.php';
        $this->smarty->display($template);
        require $this->viewsPath . '/layout/footer.php';
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0775, true)) {
            throw new RuntimeException('Impossibile creare la cartella Smarty: ' . $path);
        }
    }
}
