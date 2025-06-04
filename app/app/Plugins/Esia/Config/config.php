<?php

return [
    'cryptopro' => env('PLUGINS_ESIA_CRYPTOPRO', 'http://127.0.0.1:3037/cryptopro/sign'),
    'redirect' => env('PLUGINS_ESIA_REDIRECT', 'http://127.0.0.1/auth/esia'),
    'clientid' => env('PLUGINS_ESIA_CLIENTID', '000000'),
    'scope' => env('PLUGINS_ESIA_SCOPE', 'openid fullname birthdate gender email mobile addresses'),
];
