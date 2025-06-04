<?php

namespace App\Plugins\Esia\Services;

class CryptoPro
{
    public static function getClientSecret(string $secret): string
    {
        return json_decode(FilePostContents::return(
            config('plugins.Esia.cryptopro'),
            $secret
        ))->result;
    }
}
