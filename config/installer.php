<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Installer Route Prefix
    |--------------------------------------------------------------------------
    |
    | The route prefix for the installer routes.
    |
    */
    'route_prefix' => 'installer',

    /*
    |--------------------------------------------------------------------------
    | License API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for license verification API.
    | The API now returns SQL content directly in the license verification response.
    |
    */
    'license_api' => [
        'url' => env('INSTALLER_LICENSE_API_URL', 'https://api.yoursite.com/api/verify-license'),
        'timeout' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Configuration
    |--------------------------------------------------------------------------
    |
    | Product ID for license verification.
    |
    */
    'product_id' => env('INSTALLER_PRODUCT_ID', 1),

    /*
    |--------------------------------------------------------------------------
    | System Requirements
    |--------------------------------------------------------------------------
    |
    | Define the minimum system requirements for installation.
    |
    */
    'requirements' => [
        'php' => '8.2.0',
        'extensions' => [
            'PDO',
            'cURL',
            'OpenSSL',
            'BCMath',
            'Ctype',
            'Fileinfo',
            'JSON',
            'Mbstring',
            'Tokenizer',
            'XML',
            'ZIP',
        ],
        'folders' => [
            'storage/app/' => '775',
            'storage/framework/' => '775',
            'storage/logs/' => '775',
            'bootstrap/cache/' => '775',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Lock
    |--------------------------------------------------------------------------
    |
    | Prevent re-installation by creating a lock file.
    |
    */
    'lock_file' => storage_path('installer.lock'),

    /*
    |--------------------------------------------------------------------------
    | View Settings
    |--------------------------------------------------------------------------
    |
    | Customize the installer appearance.
    |
    */
    'theme' => [
        'primary_color' => '#3490dc',
        'logo_path' => null, // Path to your logo
    ],
];