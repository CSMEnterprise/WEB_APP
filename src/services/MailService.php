<?php

namespace App\Services;

use App\Core\Request;
use App\Core\SessionManager;

class MailService
{
    private array $config;

    public function __construct()
    {
        $configPath = __DIR__ . '/../config/mail.php';
        if (!is_file($configPath)) {
            throw new \RuntimeException('Configurazione email locale mancante. Crea src/config/mail.php con i valori del tuo ambiente.');
        }

        $this->config = require $configPath;
    }

    /** In debug mode salva il link in sessione invece di inviarlo via SMTP. */
    private function debugSalva(string $tipo, string $link, string $destinatario): void
    {
        SessionManager::start();
        SessionManager::set('debug_mail', [
            'tipo'        => $tipo,
            'link'        => $link,
            'destinatario'=> $destinatario,
            'timestamp'   => time(),
        ]);
    }

    private function crea(): object
    {
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new \RuntimeException('PHPMailer non trovato. Esegui: composer install');
        }
        require_once $autoload;

        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet    = 'UTF-8';
        $mail->Host       = $this->config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->config['username'];
        $mail->Password   = $this->config['password'];
        $mail->Port       = $this->config['port'];
        $encryption = $this->config['encryption'] ?? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        if ($encryption !== null && $encryption !== '') {
            $mail->SMTPSecure = $encryption;
        }
        $mail->setFrom($this->config['from'], $this->config['from_name']);

        return $mail;
    }

    public function inviaVerificaEmail(string $destinatario, string $nome, string $token): void
    {
        $link = $this->buildUrl('/auth/verifica-email/' . urlencode($token));

        if (!empty($this->config['debug'])) {
            $this->debugSalva('verifica', $link, $destinatario);
            return;
        }

        $mail = $this->crea();
        $mail->addAddress($destinatario, $nome ?: $destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Verifica la tua email – NerdVault';
        $mail->Body    = $this->templateEmail(
            'Verifica il tuo account',
            "Ciao <strong>" . htmlspecialchars($nome ?: $destinatario, ENT_QUOTES) . "</strong>,<br><br>
            Clicca il pulsante qui sotto per verificare il tuo indirizzo email.<br>
            Il link scadrà tra <strong>48 ore</strong>.",
            $link,
            'Verifica email'
        );
        $mail->AltBody = "Verifica la tua email su NerdVault:\n$link\n\nIl link scade tra 48 ore.";
        $mail->send();
    }

    public function inviaResetPassword(string $destinatario, string $nome, string $token): void
    {
        $link = $this->buildUrl('/auth/reset-password/' . urlencode($token));

        // ── MODALITÀ DEBUG: mostra il link a schermo, non invia email ──
        if (!empty($this->config['debug'])) {
            $this->debugSalva('reset', $link, $destinatario);
            return;
        }

        $mail = $this->crea();
        $mail->addAddress($destinatario, $nome ?: $destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Recupero password – NerdVault';
        $mail->Body    = $this->templateEmail(
            'Recupero password',
            "Ciao <strong>" . htmlspecialchars($nome ?: $destinatario, ENT_QUOTES) . "</strong>,<br><br>
            Abbiamo ricevuto una richiesta di reset della password.<br>
            Clicca il pulsante qui sotto per impostarne una nuova.<br>
            Il link scadrà tra <strong>1 ora</strong>.<br><br>
            Se non hai richiesto il reset, ignora questa email.",
            $link,
            'Reimposta password'
        );
        $mail->AltBody = "Reimposta la tua password su NerdVault:\n$link\n\nIl link scade tra 1 ora.";
        $mail->send();
    }

    private function templateEmail(string $titolo, string $corpo, string $linkUrl, string $linkTesto): string
    {
        return "
        <!DOCTYPE html>
        <html lang='it'>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;background:#0b0b14;font-family:Arial,sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#0b0b14;padding:40px 20px;'>
                <tr><td align='center'>
                    <table width='520' cellpadding='0' cellspacing='0'
                           style='background:#12121f;border-radius:16px;border:1px solid #2a2a45;overflow:hidden;'>
                        <tr>
                            <td style='background:linear-gradient(135deg,#7c3aed,#f59e0b);padding:4px;'></td>
                        </tr>
                        <tr>
                            <td style='padding:40px 40px 32px;'>
                                <p style='margin:0 0 8px;font-size:22px;font-weight:800;color:#f0f0ff;letter-spacing:-.02em;'>
                                    NerdVault
                                </p>
                                <h1 style='margin:0 0 24px;font-size:20px;font-weight:700;color:#f0f0ff;'>
                                    $titolo
                                </h1>
                                <p style='margin:0 0 32px;font-size:15px;color:#8b8bac;line-height:1.7;'>
                                    $corpo
                                </p>
                                <a href='$linkUrl'
                                   style='display:inline-block;padding:14px 28px;background:linear-gradient(135deg,#7c3aed,#9d5cf6);
                                          color:#fff;text-decoration:none;border-radius:10px;font-weight:700;font-size:15px;'>
                                    $linkTesto
                                </a>
                                <p style='margin:28px 0 0;font-size:12px;color:#5a5a7a;'>
                                    Se il pulsante non funziona, copia e incolla questo link nel browser:<br>
                                    <a href='$linkUrl' style='color:#7c3aed;word-break:break-all;'>$linkUrl</a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:20px 40px;border-top:1px solid #2a2a45;'>
                                <p style='margin:0;font-size:12px;color:#5a5a7a;'>
                                    &copy; " . date('Y') . " NerdVault &mdash; Marketplace per appassionati nerd
                                </p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>";
    }

    private function buildUrl(string $path): string
    {
        $baseUrl = trim((string) ($this->config['base_url'] ?? ''));

        if ($baseUrl === '') {
            $scheme = (!empty(Request::server('HTTPS')) && Request::server('HTTPS') !== 'off') ? 'https' : 'http';
            $host = Request::server('HTTP_HOST', 'localhost');
            $scriptDir = rtrim(str_replace('\\', '/', dirname(Request::server('SCRIPT_NAME', '/'))), '/');
            $baseUrl = $scheme . '://' . $host . ($scriptDir !== '' && $scriptDir !== '/' ? $scriptDir : '');
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
