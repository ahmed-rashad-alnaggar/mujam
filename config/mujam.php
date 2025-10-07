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
    | These configurations are based on the 'alnaggar/muhawil' package, which
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
        ],

        'json' => [
            'driver' => 'json',
            'path' => lang_path(),
            // 'flags' => JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS,
        ],

        /* 'mo' => [
            'driver' => 'mo',
            'path' => lang_path(),
            'context_delimiter' => '::',
            'plural_delimiter' => '|',
            'metadata' => [],
        ],*/

        /* 'po' => [
            'driver' => 'po',
            'path' => lang_path(),
            'context_delimiter' => '::',
            'plural_delimiter' => '|',
            'metadata' => [],
        ],*/

        /* 'xliff' => [
            'driver' => 'xliff',
            'path' => lang_path(),
            'source_locale' => null, // Use application's fallback locale
            'legacy' => false,
        ],*/

        /* 'yaml' => [
            'driver' => 'yaml',
            'path' => lang_path(),
            'dry' => true,
        ],*/

        /* 'database' => [
            'driver' => 'database',
            'connection' => null, // Use application's default database connection
            'table' => 'translations',
            'columns' => [
                'namespace' => 'namespace',
                'group' => 'group',
                'locale' => 'locale',
                'value' => 'value',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Has Translations Trait Store
    |--------------------------------------------------------------------------
    |
    | The **structured** store name/config used by the HasTranslations trait
    | for handling Eloquent model translations.
    |
    */

    'model_translations_store' => [
        'driver' => 'database',
        'connection' => null,
        'table' => 'translations',
        'columns' => [
            'namespace' => 'namespace',
            'group' => 'group',
            'locale' => 'locale',
            'value' => 'value',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ],
    ]
];
