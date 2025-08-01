<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Location',
        'Upload-Offset',
        'Upload-Length',
        'Tus-Resumable',
    ],

    'max_age' => 0,

    'supports_credentials' => false,

];
