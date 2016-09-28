<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Log To File
     |--------------------------------------------------------------------------
     |
     | If set to true, Synful will log all output to a file.
     */

    'log_to_file' => true,

    /*
     |--------------------------------------------------------------------------
     | Log File
     |--------------------------------------------------------------------------
     |
     | The file to log output to.
     */

    'logfile' => './logs/synful.log',

    /*
     |--------------------------------------------------------------------------
     | Split Log Files
     |--------------------------------------------------------------------------
     |
     | If set to true, log files will be split into a new file when the
     | exceed a certain number of lines.
     */

    'split_log_files' => true,

    /*
     |--------------------------------------------------------------------------
     | Maximum Log File Lines
     |--------------------------------------------------------------------------
     |
     | The number of lines to allow a log file to have before splitting it
     | into a new log file.
     */

    'max_logfile_lines' => 300,

];
