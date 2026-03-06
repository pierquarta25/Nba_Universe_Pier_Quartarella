<?php


// COSA FA: Centralizza le credenziali dei servizi esterni.
// Laravel legge questo file con config('services.nba.key').
//
// PERCHÉ NON USARE env() DIRETTAMENTE NEL CODICE?
// Perché in produzione Laravel mette in cache la config
// (php artisan config:cache) e env() smette di funzionare
// fuori da questo file. Usare config() è la pratica corretta.


return [

    
    // MAILGUN (già presente in Laravel di default)
    
    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    
    // NBA API — balldontlie.io
    
    'nba' => [
        'key'      => env('NBA_API_KEY', ''),
        'base_url' => env('NBA_API_BASE_URL', 'https://api.balldontlie.io/v1'),
    ],

];