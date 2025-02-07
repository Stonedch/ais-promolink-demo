<?php

namespace App\Orchid\Fields;

use Orchid\Screen\Fields\Upload;

class SingleUpload extends Upload
{
    protected $attributes = [
        'value'           => null,
        'multiple'        => false,
        'parallelUploads' => 1,
        'maxFileSize'     => null,
        'maxFiles'        => 1,
        'timeOut'         => 0,
        'acceptedFiles'   => null,
        'resizeQuality'   => 0.8,
        'resizeWidth'     => null,
        'resizeHeight'    => null,
        'media'           => false,
        'closeOnAdd'      => false,
        'visibility'      => 'public',
    ];
}
