<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => env('FILESYSTEM_LOCAL', 'local'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                ],
            ],
            'url' => '/storage',
            'visibility' => 'public',
        ],

        'backupLocal' => [
            'driver' => 'local',
            'root' => storage_path('app/public/backups'),
        ],

        'staging' => [
            'driver' => 'local',
            'root' => '/var/www/taylor-properties.net/storage/app/public',
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                ],
            ],
            'url' => '/storage',
            'visibility' => 'public',
        ],

        'staging_backups' => [
            'driver' => 'ftp',
            'host' => env('STAGING_IP'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'port' => 22,
            'root' => '/mnt/sdb/storage',
            // 'passive' => true,
            'ssl' => true,
            'timeout' => 30,
        ],

        'staging_backups' => [
            'driver' => 'sftp',
            'host' => env('STAGING_HOST'),

            // Settings for basic authentication...
            'username' => env('STAGING_USERNAME'),
            'password' => env('STAGING_PASSWORD'),

            // Settings for SSH key based authentication with encryption password...
            'privateKey' => env('STAGING_PRIVATE_KEY'),
            'password' => env('STAGING_PASSWORD'),

            // Optional SFTP Settings...
            // 'hostFingerprint' => env('SFTP_HOST_FINGERPRINT'),
            // 'maxTries' => 4,
            // 'passphrase' => env('SFTP_PASSPHRASE'),
            'port' => 22,
            'root' => '/mnt/sdb/storage',
            'timeout' => 30,
            // 'useAgent' => true,
        ],

        'public' => [
            'driver' => 'local',
            'root' => '/mnt/vol2',
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                ],
            ],
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'backup' => [
            'driver' => 'local',
            'root' => '/mnt/vol2/backups',
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0775,
                ],
            ],
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
