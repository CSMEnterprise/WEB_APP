<?php
// ============================================================
//  NerdVault — Crea account admin
//  ATTENZIONE: elimina questo file dopo l'uso!
// ============================================================

$host   = 'localhost';
$port   = '3306';
$dbname = 'nerdvault';
$dbuser = 'root';
$dbpass = '';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $livello  = (int) ($_POST['livello'] ?? 1);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email non valida.';
    } elseif (strlen($password) < 8) {
        $error = 'La password deve essere di almeno 8 caratteri.';
    } elseif ($password !== $confirm) {
        $error = 'Le password non coincidono.';
    } elseif (!in_array($livello, [1, 2])) {
        $error = 'Livello di sicurezza non valido.';
    } else {
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $dbuser, $dbpass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Controlla se l'email esiste già
            $check = $pdo->prepare('SELECT id_admin FROM admin WHERE email = ? LIMIT 1');
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'Esiste già un admin con questa email.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Verifica se la colonna stato_ban esiste
                $cols = $pdo->query("SHOW COLUMNS FROM admin LIKE 'stato_ban'")->fetchAll();
                if ($cols) {
                    $stmt = $pdo->prepare(
                        'INSERT INTO admin (email, password_hash, livello_sicurezza, stato_ban) VALUES (?, ?, ?, 0)'
                    );
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO admin (email, password_hash, livello_sicurezza) VALUES (?, ?, ?)'
                    );
                }
                $stmt->execute([$email, $hash, $livello]);
                $newId   = $pdo->lastInsertId();
                $success = "Admin creato con successo! (ID: $newId) — Ricordati di eliminare questo file.";
            }
        } catch (Exception $e) {
            $error = 'Errore database: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Crea Admin — NerdVault</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: system-ui, sans-serif;
    background: #0f0f13;
    color: #e0e0e0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .card {
    background: #1a1a24;
    border: 1px solid #2e2e42;
    border-radius: 12px;
    padding: 2rem 2.5rem;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
  }
  .card h1 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: .25rem;
    color: #fff;
  }
  .card .subtitle {
    font-size: .82rem;
    color: #f59e0b;
    margin-bottom: 1.75rem;
  }
  label {
    display: block;
    font-size: .82rem;
    color: #a0a0b8;
    margin-bottom: .35rem;
    margin-top: 1rem;
  }
  input, select {
    width: 100%;
    padding: .65rem .9rem;
    background: #0f0f18;
    border: 1px solid #2e2e42;
    border-radius: 7px;
    color: #e0e0e0;
    font-size: .95rem;
    transition: border-color .2s;
  }
  input:focus, select:focus {
    outline: none;
    border-color: #6366f1;
  }
  select option { background: #1a1a24; }
  .btn {
    margin-top: 1.75rem;
    width: 100%;
    padding: .75rem;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
  }
  .btn:hover { background: #4f46e5; }
  .alert {
    margin-top: 1.25rem;
    padding: .75rem 1rem;
    border-radius: 7px;
    font-size: .9rem;
  }
  .alert-success { background: #14532d; border: 1px solid #16a34a; color: #86efac; }
  .alert-error   { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; }
  .warning-box {
    margin-top: 1.5rem;
    padding: .65rem .9rem;
    background: #451a03;
    border: 1px solid #92400e;
    border-radius: 7px;
    font-size: .78rem;
    color: #fcd34d;
    line-height: 1.5;
  }
</style>
</head>
<body>
<div class="card">
  <h1>🛡️ Crea Account Admin</h1>
  <p class="subtitle">⚠️ Strumento temporaneo — elimina il file dopo l'uso</p>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!$success): ?>
  <form method="POST" autocomplete="off">
    <label for="email">Email admin</label>
    <input type="email" id="email" name="email"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
           placeholder="admin@nerdvault.it" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password"
           placeholder="Minimo 8 caratteri" required>

    <label for="confirm">Conferma password</label>
    <input type="password" id="confirm" name="confirm"
           placeholder="Ripeti la password" required>

    <label for="livello">Livello di sicurezza</label>
    <select id="livello" name="livello">
      <option value="1" <?= ($_POST['livello'] ?? '1') === '1' ? 'selected' : '' ?>>
        1 — Admin standard
      </option>
      <option value="2" <?= ($_POST['livello'] ?? '') === '2' ? 'selected' : '' ?>>
        2 — Super admin (può bannare altri admin)
      </option>
    </select>

    <button type="submit" class="btn">Crea account admin</button>
  </form>
  <?php endif; ?>

  <div class="warning-box">
    Questo file non è protetto da autenticazione.<br>
    <strong>Eliminalo subito dopo aver creato l'account.</strong>
  </div>
</div>
</body>
</html>
