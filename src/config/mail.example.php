<?php

/*
|--------------------------------------------------------------------------
| Configurazione email NerdVault
|--------------------------------------------------------------------------
| Copiare questo file in src/config/mail.php e adattare i valori locali.
| Con debug=true i link email vengono salvati in sessione invece di inviare SMTP.
|--------------------------------------------------------------------------
*/

return [
    'debug' => true,
    'base_url' => 'http://nerdvault.local',

    'host' => 'smtp.example.com',
    'port' => 587,
    'username' => '',
    'password' => '',
    'from' => 'no-reply@nerdvault.local',
    'from_name' => 'NerdVault',
];
