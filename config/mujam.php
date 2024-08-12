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
    | If multiple stores provide the same translation for the same key,
    | the last one defined will override the previous ones.
    |
    | Supported drivers: "database", "json", "mo", "php", "po", "xliff", "yaml".
    |
    | All file-based drivers require only two parameters: "driver" and "paths".
    | The "paths" parameter can be a single path or an array of paths.
    | An optional "metadata" parameter may be specified to include
    | additional metadata for the file if applicable.
    |
    | The "database" driver requires two parameters: "driver" and "table".
    | An optional "connection" parameter can be specified to use a different
    | database connection than the default. Assigning null will use the default connection.
    |
    */

    'stores' => [
        'php' => [
            'driver' => 'php',
            'paths' => lang_path(),
        ],

        'json' => [
            'driver' => 'json',
            'paths' => lang_path(),
        ],

        // Example configuration for a database-based store.
        /* 'database' => [
             'driver' => 'database',
             'connection' => null, // Use default connection.
             'table' => 'translations',
         ],*/
    ],
];
