<?php

return [
    'active' => env('PLUGINS_ACTIVE', ''), // example 'ExamplePlugin;CustomReportLoaderPlugin;FunnyCatPlugin'
    'paths' => [
        'plugins' => app_path('Plugins'),
    ],
];
