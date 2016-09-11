<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Synful Server Configuration
     |--------------------------------------------------------------------------
     |
     | Add all MySql Servers and child database entries here.
     */

    /*
     |--------------------------------------------------------------------------
     | Primary Synful Server
     |--------------------------------------------------------------------------
     |
     | The primary server housing the main Synful database.
     | Synful will automatically look for the primary synful database under
     | 'sqlservers.main.databases.synful'.
     */

    'main' => [

        'host' => '127.0.0.1',

        'port' => 3306,

        'databases' => [

            'synful' => [

                'username' => 'root',

                'password' => 'aoc1080p',

                'database' => 'synful',

            ],

            /*
             |------------------------------------------------------------------
             | Import Example
             |------------------------------------------------------------------
             |
             | This is an example of using the 'use' setting to import settings
             | from another database entry.
             |
             | !! Remove this in production.


            'users' => [

                /*
                 |--------------------------------------------------------------
                 | Use Setting
                 |--------------------------------------------------------------
                 |
                 | If this setting is set, the config will import settings
                 | from the defined database entry.

                'use' => 'synful',

                /*
                 |--------------------------------------------------------------
                 | Database Name
                 |--------------------------------------------------------------
                 |
                 | The name of this MySql Database. This will override any
                 | imported settings.

                'database' => 'users',

             ],

             */

        ],

    ],

];
