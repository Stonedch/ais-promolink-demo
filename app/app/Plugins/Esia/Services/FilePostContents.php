<?php

namespace App\Plugins\Esia\Services;

class FilePostContents
{
    public static function return(string $url, string $data): string
    {
        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => 'text=' . urlencode($data)
            )
        );

        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }
}
