<?php
/**
 * Script una-tantum per creare un account admin.
 * Aprilo nel browser, poi CANCELLALO subito dopo.
 * http://localhost/WEB_APP/public/crea_admin.php
 */

require_once __DIR__ . '/../src/config/db.php';

// ─── CONFIGURA QUI ────────────────────────────────────────────
$email    = 'admin@nerdvault.it';
$password = 'Admin@2024!';       // min 10 car., 1 maiuscola, 1 speciale
$livello  = 1;
// ──────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? $email);
    $password = trim($_POST['password'] ?? '');
    $livello  = (int) ($_POST['livello'] ?? 1);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email non valida.';
    }
    if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}$/', $password)) {
        $errors[] = 'La password deve avere almeno 10 caratteri, una maiuscola e un carattere speciale.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admin (email, password_hash, livello_sicurezza)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $livello]);
            $success = 'Admin creato con successo! Ora cancella questo file.';
        } catch (PDOException $e) {
            $errors[] = 'Errore: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Crea Admin - NerdVault</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 480px; margin: 60px auto; background: #f5f5f5; }
        .card { background: white; padding: 28px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1 { margin-top: 0; }
        label { display: block; font-weight: bold; margin-top: 14px; }
        input, select { width: 100%; padding: 10px; margin-top: 4px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        button { margin-top: 20px; width: 100%; padding: 12px; background: #2563eb; color: white; border: 0; border-radius: 8px; font-size: 15px; cursor: pointer; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 12px; }
        .success { background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; margin-bottom: 12px; font-weight: bold; }
        .warning { background: #fef9c3; color: #854d0e; padding: 10px; border-radius: 8px; margin-top: 16px; font-size: 13px; }
    </style>
</head>
<body>
<div class="card">
    <h1>Crea account Admin</h1>

    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <?php if (empty($success)): ?>
    <form method="post">
        <label for="email">Email admin</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required
               placeholder="Min 10 car., 1 maiuscola, 1 speciale">

        <label for="livello">Livello sicurezza</label>
        <select id="livello" name="livello">
            <option value="1">1 - Standard</option>
            <option value="2">2 - Elevato</option>
        </select>

        <button type="submit">Crea Admin</button>
    </form>
    <?php endif; ?>

    <div class="warning">
        ⚠️ <strong>Attenzione:</strong> cancella questo file non appena hai creato l'admin.
    </div>
</div>
</body>
</html>
