<?php

return [
    'paths' => [resource_path('views')],
    'manifest' => public_path('build/manifest.json'),
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
];
