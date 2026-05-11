<?php
$pageTitle = 'Area business';
require __DIR__ . '/../layout/header.php';
?>

<h1>Area business</h1>

<?php if (!empty($business)): ?>
    <section class="card">
        <h2><?= e($business['nome_azienda'] ?? '') ?></h2>
        <p><strong>Partita IVA:</strong> <?= e($business['p_iva'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= e($business['email_aziendale'] ?? '') ?></p>
        <p><strong>Telefono:</strong> <?= e($business['telefono'] ?? '') ?></p>
        <p><strong>Verificato:</strong> <?= !empty($business['verificato']) ? 'Sì' : 'No' ?></p>
        <p><?= e($business['descrizione'] ?? '') ?></p>
    </section>

    <h2>I miei annunci</h2>

    <?php if (!empty($annunci)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Prezzo</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($annunci as $annuncio): ?>
                    <tr>
                        <td><?= e($annuncio['titolo'] ?? '') ?></td>
                        <td>€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></td>
                        <td><?= e($annuncio['stato'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nessun annuncio pubblicato.</p>
    <?php endif; ?>

    <p>
        <a class="btn" href="index.php?route=business-ordini">Ordini ricevuti</a>
    </p>
<?php else: ?>
    <div class="card">
        <p>Non hai ancora un account business.</p>
        <a class="btn" href="index.php?route=business-create">Crea account business</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
