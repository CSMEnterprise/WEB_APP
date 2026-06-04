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
    'debug' => false,
    'base_url' => 'http://localhost/WEB_APP/public',

    'host' => 'sandbox.smtp.mailtrap.io',
    'port' => 2525,
    'encryption' => 'tls',
    'username' => 'MAILTRAP_USERNAME',
    'password' => 'MAILTRAP_PASSWORD',
    'from' => 'no-reply@nerdvault.local',
    'from_name' => 'NerdVault',
];
