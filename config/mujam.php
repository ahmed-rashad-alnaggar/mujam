<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Translation Store
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the translation stores below you wish
    | to use as your default store.
    |
    */

    'default' => 'php',

    /*
    |--------------------------------------------------------------------------
    | Translation Stores
    |--------------------------------------------------------------------------
    |           
    | Define all the translation "stores" for your application here.
    |
    | If multiple stores provide a translation for the same key,
    | the last one defined will override the previous ones.
    |
    | Supported drivers: "database", "json", "mo", "php", "po", "xliff", "yaml".
    |
    | File-based drivers require two parameters: "driver" and "path".
    | The "path" parameter can be a single path or an array of paths.
    | Each driver has its own configuration as defined below.
    | These configurations are based on the 'alnaggar/php-translation-files' package, which
    | is used for loading and dumping translations.
    |
    | The "database" driver requires two parameters: "driver" and "table".
    | An optional "connection" parameter can be specified to use a different
    | database connection than application's default connection. Assigning `null` will
    | use application's default database connection.
    |
    */

    'stores' => [
        'php' => [
            'driver' => 'php',
            'path' => lang_path(),
            'cache' => [
                'enabled' => true,
                'store' => null, // Use application's default cache store
                'prefix' => 'mujam.php',
                'lifetime' => 9999999999, // Forever
            ],
        ],

        'json' => [
            'driver' => 'json',
            'path' => lang_path(),
            //'flags' => JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS,
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.json',
                'lifetime' => 9999999999,
            ],
        ],

        /*'mo' => [
            'driver' => 'mo',
            'path' => lang_path(),
            'context_delimiter' => '::',
            'plural_delimiter' => '|',
            'metadata' => [],
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.mo',
                'lifetime' => 9999999999,
            ],
        ],*/

        /*'po' => [
            'driver' => 'po',
            'path' => lang_path(),
            'context_delimiter' => '::',
            'plural_delimiter' => '|',
            'metadata' => [],
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.po',
                'lifetime' => 9999999999,
            ],
        ],*/

        /*'xliff' => [
            'driver' => 'xliff',
            'path' => lang_path(),
            'source_locale' => null, // Use application's fallback locale
            'legacy' => false,
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.xliff',
                'lifetime' => 9999999999,
            ],
        ],*/

        /*'yaml' => [
            'driver' => 'yaml',
            'path' => lang_path(),
            'dry' => true,
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.yaml',
                'lifetime' => 9999999999,
            ],
        ],*/

        /*'database' => [
            'driver' => 'database',
            'connection' => null, // Use application's default database connection
            'table' => 'translations',
            'columns' => [
                'namespace' => 'namespace',
                'group' => 'group',
                'item'=> 'item',
                'locale' => 'locale',
                'value' => 'value',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
            'cache' => [
                'enabled' => true,
                'store' => null,
                'prefix' => 'mujam.database',
                'lifetime' => 9999999999,
            ],
        ],*/
    ],
];
