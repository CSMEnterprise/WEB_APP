<?php
require __DIR__ . '/vendor/autoload.php';

$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/templates');
$compileDir = __DIR__ . '/_cc_tmp';
if (!is_dir($compileDir)) { mkdir($compileDir, 0777, true); }
$smarty->setCompileDir($compileDir);
$smarty->force_compile = true;

// Modificatori custom usati nei template (registrati dall'app in runtime)
$smarty->registerPlugin('modifier', 'count_items', function ($v) {
    return is_array($v) || $v instanceof Countable ? count($v) : ($v ? 1 : 0);
});
$smarty->registerPlugin('modifier', 'nl2br_e', function ($v) {
    return nl2br(htmlspecialchars((string) $v));
});

$files = [
    'annunci/lista.tpl',
    'annunci/dettaglio.tpl',
    'carrello/lista.tpl',
    'auth/login.tpl',
    'auth/register.tpl',
    'utenti/profilo.tpl',
];

$fail = 0;
foreach ($files as $f) {
    try {
        $tpl = $smarty->createTemplate($f);
        $tpl->compileTemplateSource(); // compila solo il sorgente, niente render
        echo "OK    $f\n";
    } catch (Throwable $e) {
        $fail++;
        echo "ERROR $f\n      " . $e->getMessage() . "\n";
    }
}
echo $fail === 0 ? "\nTUTTI OK\n" : "\n$fail FALLITI\n";
