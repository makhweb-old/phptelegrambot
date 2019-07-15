<?php

return [
    /**
     * Bot configuration
     */
    'bot'      => [
        'name'    => env('PHP_TELEGRAM_BOT_NAME', ''),
        'api_key' => env('PHP_TELEGRAM_BOT_API_KEY', ''),
    ],

    'owm_api_key' => '4ff797ad8c41dbc7789afc2d15847c73',

    /**
     * Database integration
     */
    'database' => [
        'enabled'    => true,
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

    'commands' => [
        'before'  => true,
        'paths'   => [
            app_path('Telegram/Commands')
        ],
        'configs' => [
            'sendtochannel'=>[
                'your_channel' => [
                    '@hdhjsuseisjs',
                ]
            ]
        ],
    ],

    'admins'  => [
        719641916
    ],

    /**
     * Request limiter
     */
    'limiter' => [
        'enabled'  => false,
        'interval' => 1,
    ],

    'upload_path'   => '',
    'download_path' => '',
];
