<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Synful Database Entry
     |--------------------------------------------------------------------------
     |
     | This database is used to store API Keys. It is also treated as the
     | default database unless you specify otherwise in your model or
     | migrations `$connection` variable.
     */

    'synful' => [
        'driver'    => 'mysql',
        'host'      => '192.168.1.85',
        'database'  => 'synful',
        'username'  => 'root',
        'password'  => 'password',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ],
];
