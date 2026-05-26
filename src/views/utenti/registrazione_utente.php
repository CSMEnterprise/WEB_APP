<?php
$pageTitle = 'Registrazione utente';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card u-style-123">
    <div class="u-style-111">
        <div class="u-style-124">ACCOUNT PERSONALE</div>
        <h1 class="u-style-113">Registrazione Utente</h1>
        <p class="muted u-style-094">Crea un account personale per acquistare, vendere e salvare annunci su NerdVault.</p>
    </div>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-user-post">

        <div class="grid-2 u-style-114">
            <!-- Profilo -->
            <div class="u-style-115">
                <h3 class="u-style-116">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profilo utente
                </h3>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" pattern="[A-Za-z0-9_.-]{3,30}" minlength="3" maxlength="30" title="Usa 3-30 caratteri: lettere, numeri, punto, trattino o underscore." required>

                <label for="nome">Nome completo (Opzionale)</label>
                <input type="text" id="nome" name="nome" value="<?= e($_POST['nome'] ?? '') ?>" pattern="[\p{L} .'-]{2,50}" title="Inserisci 2-50 caratteri validi.">

                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>" pattern="\+?[0-9 ]{8,15}" title="Inserisci 8-15 cifre; puoi iniziare con +." required>
            </div>

            <!-- Dati Accesso -->
            <div class="u-style-115">
                <h3 class="u-style-116">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Sicurezza e accesso
                </h3>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>

                <label for="userRegisterPassword">Password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="userRegisterPassword" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}" title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale." autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPassword', this)">Mostra</button>
                </div>

                <label for="userRegisterPasswordConfirm">Conferma password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="userRegisterPasswordConfirm" name="password_confirm" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}" title="Ripeti la stessa password scelta sopra." autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPasswordConfirm', this)">Mostra</button>
                </div>
            </div>
        </div>

        <div class="u-style-120">
            <button class="btn u-style-121" type="submit">Crea account utente</button>
            
            <p class="u-style-122">
                Vuoi scegliere un altro tipo di account? <a class="u-style-102" href="index.php?route=register">Scelta registrazione</a><br>
                Hai già un account? <a class="u-style-102" href="index.php?route=login">Accedi qui</a>
            </p>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
