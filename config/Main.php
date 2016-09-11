<?php
return [
    
    /*
     |--------------------------------------------------------------------------
     | Synful Configuration
     |--------------------------------------------------------------------------
     | All configuration changes should be made to the files in the
     | config directory. If you want to add more confifurations
     | create a new section here and require a config file.
     */

    /*
     |--------------------------------------------------------------------------
     | Main System Configuration
     |--------------------------------------------------------------------------
     |
     | Load all configuration settings for main system section of config.
     */

    'system' => require './config/System.php',

    /*
     |--------------------------------------------------------------------------
     | Database Configuration
     |--------------------------------------------------------------------------
     |
     | Load all database configurations.
     */

    'sqlservers' => require './config/SqlServers.php',

    /*
     |--------------------------------------------------------------------------
     | File Configuration
     |--------------------------------------------------------------------------
     |
     | Load all file configuration settings.
     */

    'files' => require './config/Files.php',

    /*
     |--------------------------------------------------------------------------
     | Security Configuration
     |--------------------------------------------------------------------------
     |
     | Load all security configurations.
     */

    'security' => require './config/Security.php',

    /*
     |--------------------------------------------------------------------------
     | Default Permissions Configuration
     |--------------------------------------------------------------------------
     |
     | Load all default permissions into system
     */
    
    'permissions' => require './config/Permissions.php',

];
