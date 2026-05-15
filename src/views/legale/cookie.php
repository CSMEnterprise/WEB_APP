<?php
$pageTitle = 'Cookie Policy';
require __DIR__ . '/../layout/header.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto; color: var(--text);">
    <h1 style="font-size: 32px; margin-bottom: 24px;">Cookie Policy</h1>
    <p style="color: var(--muted); margin-bottom: 20px;">Ultimo aggiornamento: <?= date('d/m/Y') ?></p>
    
    <div style="background: var(--bg-card); padding: 30px; border-radius: 16px; border: 1px solid var(--border); line-height: 1.6;">
        <p>Questa è la Cookie Policy fittizia di NerdVault.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">1. Cosa sono i Cookie?</h3>
        <p>I cookie sono piccoli file di testo che i siti salvano sul tuo computer o dispositivo mobile durante la navigazione.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">2. Come li usiamo</h3>
        <p>Utilizziamo i cookie esclusivamente per scopi tecnici (come mantenere attiva la tua sessione di accesso al sito) e non per il tracciamento di terze parti.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">3. Gestione dei Cookie</h3>
        <p>Puoi eliminare o bloccare i cookie tramite le impostazioni del tuo browser, ma alcune funzioni del sito potrebbero non funzionare correttamente.</p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
