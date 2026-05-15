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

        <div class="grid-2" style="gap: 40px; margin-bottom: 30px;">
            <!-- Dati Accesso -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--border);">
                <h3 style="color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Dati di accesso
                </h3>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" pattern="[A-Za-z0-9_.-]{3,30}" minlength="3" maxlength="30" title="Usa 3-30 caratteri: lettere, numeri, punto, trattino o underscore." required>

                <label for="email">Email accesso</label>
                <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>

                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>" pattern="\+?[0-9 ]{8,15}" title="Inserisci 8-15 cifre; puoi iniziare con +." required>

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

            <!-- Dati Business -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--border);">
                <h3 style="color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    Dati aziendali
                </h3>

                <label for="nome_azienda">Nome azienda</label>
                <input type="text" id="nome_azienda" name="nome_azienda" value="<?= e($_POST['nome_azienda'] ?? '') ?>" pattern="[\p{L}0-9 .&'-]{2,80}" minlength="2" maxlength="80" title="Inserisci 2-80 caratteri validi." required>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="margin-bottom: -16px;">
                        <label for="p_iva">Partita IVA</label>
                        <input type="text" id="p_iva" name="p_iva" value="<?= e($_POST['p_iva'] ?? '') ?>" pattern="[0-9]{11}" maxlength="11" inputmode="numeric" title="La partita IVA deve contenere esattamente 11 cifre." required>
                    </div>
                    <div style="margin-bottom: -16px;">
                        <label for="email_aziendale">Email aziendale</label>
                        <input type="email" id="email_aziendale" name="email_aziendale" value="<?= e($_POST['email_aziendale'] ?? '') ?>" required>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border); margin: 32px 0 20px;"></div>
                
                <h4 style="margin-bottom: 16px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted);">Indirizzo Sede Legale</h4>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div style="margin-bottom: -16px;">
                        <label for="via">Via / Piazza</label>
                        <input type="text" id="via" name="via" value="<?= e($_POST['via'] ?? '') ?>">
                    </div>
                    <div style="margin-bottom: -16px;">
                        <label for="numero">Civico</label>
                        <input type="text" id="numero" name="numero" value="<?= e($_POST['numero'] ?? '') ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 2fr 1fr; gap: 16px; margin-top: 16px;">
                    <div style="margin-bottom: -16px;">
                        <label for="cap">CAP</label>
                        <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" title="Il CAP deve contenere 5 cifre." value="<?= e($_POST['cap'] ?? '') ?>">
                    </div>
                    <div style="margin-bottom: -16px;">
                        <label for="citta">Città</label>
                        <input type="text" id="citta" name="citta" pattern="[\p{L} .'-]{2,80}" title="Inserisci 2-80 caratteri validi." value="<?= e($_POST['citta'] ?? '') ?>">
                    </div>
                    <div style="margin-bottom: -16px;">
                        <label for="provincia">Prov.</label>
                        <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{2}" title="Inserisci 2 lettere, ad esempio TO." value="<?= e($_POST['provincia'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <button class="btn btn-gold" type="submit" style="font-size: 16px; padding: 14px 40px;">Crea account business</button>
            
            <p style="margin-top: 24px; color: var(--muted);">
                Vuoi creare un account normale? <a href="index.php?route=register-user" style="color: var(--accent);">Registrati come utente</a><br>
                Hai già un account? <a href="index.php?route=login" style="color: var(--accent);">Accedi</a>
            </p>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
