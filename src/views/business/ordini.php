<?php
$pageTitle = 'Ordini ricevuti';
require __DIR__ . '/../layout/header.php';
?>

<h1>Ordini ricevuti</h1>

<?php if (!empty($ordini)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Annuncio</th>
                <th>Importo</th>
                <th>Stato</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ordini as $ordine): ?>
                <tr>
                    <td><?= e($ordine['id_pagamento'] ?? '') ?></td>
                    <td><?= e($ordine['titolo'] ?? '') ?></td>
                    <td>€ <?= number_format((float)($ordine['importo_totale'] ?? 0), 2, ',', '.') ?></td>
                    <td><?= e($ordine['stato'] ?? '') ?></td>
                    <td><?= e($ordine['data'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="card">
        <p>Non ci sono ordini ricevuti.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
