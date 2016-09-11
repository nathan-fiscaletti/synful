<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Standalone Mode
     |--------------------------------------------------------------------------
     |
     | If set to true, the system will run in standalone mode.
     */

    'standalone' => false,

    /*
     |--------------------------------------------------------------------------
     | Standalone IP Address
     |--------------------------------------------------------------------------
     |
     | The IP Address to run the listen server on if running in
     | standalone mode.
     */

    'ip' => '127.0.0.1',

    /*
     |--------------------------------------------------------------------------
     | Standalone Port
     |--------------------------------------------------------------------------
     |
     | The port to run the listen server on if running in standalone mode.
     */

    'port' => 7001,

    /*
     |--------------------------------------------------------------------------
     | Multithread
     |--------------------------------------------------------------------------
     |
     | If set to true, will run with multithread support when
     | running in standalone mode.
     */

    'multithread' => false,

    /*
     |--------------------------------------------------------------------------
     | Console Colors
     |--------------------------------------------------------------------------
     |
     | If set to true, color codes will be enabled.
     */

    'color' => true,

    /*
     |--------------------------------------------------------------------------
     | Production Mode
     |--------------------------------------------------------------------------
     |
     | If set to true, line numbers and file names will not be output
     | with error messages.
     */

    'production' => true,

];