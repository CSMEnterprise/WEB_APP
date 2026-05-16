<?php
$pageTitle = 'Registrazione business';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card" style="max-width: 960px; margin: 0 auto; border-top: 4px solid var(--gold); padding: 40px;">
    <div style="text-align: center; margin-bottom: 36px;">
        <div style="display: inline-block; padding: 6px 16px; background: rgba(250,204,21,.1); color: var(--gold); border-radius: 20px; font-weight: 800; font-size: 12px; letter-spacing: 0.05em; margin-bottom: 16px;">ACCOUNT PROFESSIONALE</div>
        <h1 style="margin-bottom: 12px; font-size: 32px;">Registrazione Business</h1>
        <p class="muted" style="font-size: 16px;">Apri il tuo negozio su NerdVault e raggiungi migliaia di appassionati.</p>
    </div>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-business-post">
        <input type="hidden" name="_business_registration" value="1">

        <div class="grid-2" style="gap: 40px; margin-bottom: 30px;">
            <!-- Profilo business -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--border);">
                <h3 style="color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9h1"></path><path d="M9 13h1"></path><path d="M9 17h1"></path></svg>
                    Profilo business
                </h3>

                <label for="nome_azienda">Nome azienda</label>
                <input type="text" id="nome_azienda" name="nome_azienda" value="<?= e($_POST['nome_azienda'] ?? '') ?>" pattern="[\p{L}0-9 .&'-]{2,80}" minlength="2" maxlength="80" title="Inserisci 2-80 caratteri validi." required>

                <label for="p_iva">Partita IVA</label>
                <input type="text" id="p_iva" name="p_iva" value="<?= e($_POST['p_iva'] ?? '') ?>" pattern="[0-9]{11}" maxlength="11" inputmode="numeric" title="La partita IVA deve contenere esattamente 11 cifre." required>

                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>" pattern="\+?[0-9 ]{8,15}" title="Inserisci 8-15 cifre; puoi iniziare con +." required>
            </div>

            <!-- Dati accesso -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--border);">
                <h3 style="color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Sicurezza e accesso
                </h3>

                <label for="email_aziendale">Email aziendale</label>
                <input type="email" id="email_aziendale" name="email_aziendale" value="<?= e($_POST['email_aziendale'] ?? '') ?>" required>

                <label for="businessPassword">Password</label>
                <div class="password-wrapper" style="max-width: none;">
                    <input type="password" id="businessPassword" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}" autocomplete="new-password" title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale." required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPassword', this)">Mostra</button>
                </div>

                <label for="businessPasswordConfirm">Conferma password</label>
                <div class="password-wrapper" style="max-width: none;">
                    <input type="password" id="businessPasswordConfirm" name="password_confirm" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}" autocomplete="new-password" title="Ripeti la stessa password scelta sopra." required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPasswordConfirm', this)">Mostra</button>
                </div>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 30px;">
            <h3 style="color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Indirizzo sede
            </h3>

            <div class="grid-2" style="gap: 24px;">
                <div>
                    <label for="via">Via / Corso / Piazza</label>
                    <input type="text" id="via" name="via" value="<?= e($_POST['via'] ?? '') ?>">
                </div>

                <div>
                    <label for="numero">Numero civico</label>
                    <input type="text" id="numero" name="numero" value="<?= e($_POST['numero'] ?? '') ?>">
                </div>

                <div>
                    <label for="cap">CAP</label>
                    <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" title="Il CAP deve contenere 5 cifre." value="<?= e($_POST['cap'] ?? '') ?>">
                </div>

                <div>
                    <label for="citta">Citta</label>
                    <input type="text" id="citta" name="citta" pattern="[\p{L} .'-]{2,80}" title="Inserisci 2-80 caratteri validi." value="<?= e($_POST['citta'] ?? '') ?>">
                </div>

                <div>
                    <label for="provincia">Provincia</label>
                    <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{2}" title="Inserisci 2 lettere, ad esempio TO." value="<?= e($_POST['provincia'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <button class="btn" type="submit" style="font-size: 16px; padding: 14px 40px;">Crea account business</button>

            <p style="margin-top: 24px; color: var(--muted);">
                Vuoi scegliere un altro tipo di account? <a href="index.php?route=register" style="color: var(--gold);">Scelta registrazione</a><br>
                Hai gia un account? <a href="index.php?route=login" style="color: var(--gold);">Accedi qui</a>
            </p>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
