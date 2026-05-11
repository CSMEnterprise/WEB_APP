<?php
$pageTitle = 'Registrazione';
require __DIR__ . '/../layout/header.php';
?>

<section class="card auth-card">
    <h1>Crea account</h1>
    <p class="muted">Registrati per pubblicare annunci, acquistare prodotti e gestire preferiti.</p>

    <form method="post" action="index.php?action=register" class="form">
        <div class="grid-2">
            <label>
                Username
                <input type="text" name="username" required maxlength="100">
            </label>

            <label>
                Nome
                <input type="text" name="nome" maxlength="100">
            </label>
        </div>

        <label>
            Email
            <input type="email" name="email" required autocomplete="email">
        </label>

        <label>
            Password
            <input type="password" name="password" required autocomplete="new-password">
        </label>

        <label>
            Telefono
            <input type="text" name="telefono" maxlength="20">
        </label>

        <label>
            Indirizzo
            <textarea name="indirizzo" rows="3"></textarea>
        </label>

        <button class="btn" type="submit">Registrati</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
