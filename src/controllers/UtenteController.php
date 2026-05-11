<?php

class UtenteController
{
    public function login(array $data): void
    {
        global $pdo;

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM Utente_Registrato WHERE email = ?");
        $stmt->execute([$email]);

        $utente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$utente || !password_verify($password, $utente['password_hash'])) {
            echo "Credenziali non valide";
            return;
        }

        if ($utente['stato_ban']) {
            echo "Account bannato";
            return;
        }

        $_SESSION['user_id'] = $utente['id_utente'];
        $_SESSION['username'] = $utente['username'];

        header("Location: index.php?action=profilo");
        exit;
    }

    public function register(array $data): void
    {
        global $pdo;

        $email = $data['email'] ?? '';
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($username) || empty($password)) {
            echo "Compila tutti i campi obbligatori";
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Utente_Registrato 
                (email, username, password_hash)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$email, $username, $passwordHash]);

            header("Location: index.php?action=login");
            exit;

        } catch (PDOException $e) {
            echo "Errore registrazione: email o username già usati";
        }
    }

    public function profilo(): void
    {
        global $pdo;

        $idUtente = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM Utente_Registrato WHERE id_utente = ?");
        $stmt->execute([$idUtente]);

        $utente = $stmt->fetch(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/utenti/profilo.php';
    }
}
