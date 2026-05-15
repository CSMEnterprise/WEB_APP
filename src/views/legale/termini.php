<?php
$pageTitle = 'Termini di Servizio';
require __DIR__ . '/../layout/header.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto; color: var(--text);">
    <h1 style="font-size: 32px; margin-bottom: 24px;">Termini di Servizio</h1>
    <p style="color: var(--muted); margin-bottom: 20px;">Ultimo aggiornamento: <?= date('d/m/Y') ?></p>
    
    <div style="background: var(--bg-card); padding: 30px; border-radius: 16px; border: 1px solid var(--border); line-height: 1.6;">
        <p>Benvenuto nei Termini di Servizio fittizi di NerdVault.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">1. Accettazione</h3>
        <p>Utilizzando NerdVault accetti di rispettare questi termini e condizioni. Se non sei d'accordo, ti invitiamo a non usare il servizio.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">2. Comportamento Utente</h3>
        <p>Gli utenti si impegnano a non pubblicare materiale illecito, offensivo o truffaldino. NerdVault si riserva il diritto di bannare gli account non conformi.</p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--accent);">3. Transazioni</h3>
        <p>NerdVault funge da tramite. Decliniamo ogni responsabilità in caso di disaccordi diretti tra acquirente e venditore non coperti dalle politiche di tutela standard.</p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
