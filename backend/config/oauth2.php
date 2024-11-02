<?php 

declare(strict_types=1);

return [
    'google' => [
        'clientId' => '%env(resolve:GOOGLE_CLIENT_ID)%',
        'clientSecret' => '%env(resolve:GOOGLE_CLIENT_SECRET)%',
        'redirectUri' => '%env(resolve:GOOGLE_REDIRECT_URI_DEV)%',
    ],
];