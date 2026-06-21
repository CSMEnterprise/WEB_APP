<?php

/*
|--------------------------------------------------------------------------
| Configurazione email NerdVault
|--------------------------------------------------------------------------
| Copiare questo file in src/config/mail.php e inserire le credenziali SMTP
| del proprio inbox Mailtrap.
|--------------------------------------------------------------------------
*/

return [
    // In una nuova installazione i link vengono mostrati nell'app senza SMTP.
    // In produzione impostare MAIL_DEBUG=0 e fornire credenziali reali.
    'debug' => filter_var(getenv('MAIL_DEBUG') ?: '1', FILTER_VALIDATE_BOOL),
    'base_url' => getenv('APP_BASE_URL') ?: 'http://nerdvault.local',

    'host' => getenv('MAIL_HOST') ?: 'sandbox.smtp.mailtrap.io',
    'port' => (int) (getenv('MAIL_PORT') ?: 2525),
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'username' => getenv('MAIL_USERNAME') ?: 'MAILTRAP_USERNAME',
    'password' => getenv('MAIL_PASSWORD') ?: 'MAILTRAP_PASSWORD',
    'from' => getenv('MAIL_FROM') ?: 'no-reply@nerdvault.local',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'NerdVault',
];
