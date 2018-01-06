<?php

return [

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

    /*
     |--------------------------------------------------------------------------
     | Display Errors
     |--------------------------------------------------------------------------
     |
     | If set to true, fatal errors will be displayed.
     */

    'display_errors' => false,

    /*
     |--------------------------------------------------------------------------
     | Pretty Responses
     |--------------------------------------------------------------------------
     |
     | If set to true, JSON responses will be formatted using JSON_PRETTY_PRINT.
     */

    'pretty_responses' => false,

    /*
     |--------------------------------------------------------------------------
     | Allow Get Pretty Responses
     |--------------------------------------------------------------------------
     |
     | If set to true, JSON responses will be formatted using JSON_PRETTY_PRINT
     | when the `pretty` $_GET parameter is passed.
     */

    'allow_pretty_responses_on_get' => true,

    /*
     |--------------------------------------------------------------------------
     | Global Middleware
     |--------------------------------------------------------------------------
     |
     | The select middleware that will be applied to all RequestHandlers.
     */

    'global_middleware' => [],

];
