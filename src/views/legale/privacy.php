<?php
$pageTitle = 'Privacy Policy';
require __DIR__ . '/../layout/header.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto; color: var(--text);">
    <h1 style="font-size: 32px; margin-bottom: 24px;">Privacy Policy</h1>
    <p style="color: var(--muted); margin-bottom: 20px;">Ultimo aggiornamento: <?= date('d/m/Y') ?></p>
    
    <div style="background: var(--bg-card); padding: 30px; border-radius: 16px; border: 1px solid var(--border); line-height: 1.6;">
        <p>Benvenuto nella finta Privacy Policy di NerdVault. Poiché questo è un progetto accademico o dimostrativo, nessun dato reale viene venduto a terze parti.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">1. Raccolta Dati</h3>
        <p>Raccogliamo l'email e le informazioni base del profilo necessarie per il funzionamento dell'account e delle transazioni.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">2. Uso dei Dati</h3>
        <p>I tuoi dati sono utilizzati per la messaggistica, la creazione di annunci e il corretto indirizzamento delle spedizioni.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">3. Sicurezza</h3>
        <p>Proteggiamo i tuoi dati adottando le opportune misure di sicurezza. Le password sono criptate.</p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
