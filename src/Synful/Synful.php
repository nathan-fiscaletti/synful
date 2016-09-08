<?php

namespace Synful;

use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;
use Synful\Standalone\Standalone;
use Synful\DataManagement\SqlConnection;
use Synful\CLIParser\CLIParser;

class Synful
{
    /**
     * The config for the system pulled from config.ini.
     *
     * @var array
     */
    public static $config;

    /**
     * The primary MySql Connection.
     *
     * @var Synful\DataManagement\SqlConnection
     */
    public static $sql;

    /**
     * The primary controller.
     *
     * @var Synful\Controller
     */
    public static $controller;

    /**
     * All SqlConnections based off of database definitions in config.
     *
     * @var array
     */
    public static $sql_databases = [];

    /**
     * All request handlers registered in the system.
     *
     * @var array
     */
    public static $request_handlers = [];

    /**
     * Initialize the Synful API instance using either Standalone Mode or Local Web Server.
     */
    public static function initialize()
    {
        // Make sure we aren't using that pesky PHP < 7.0
        0 <=> 0;

        // Load console color codes
        Colors::loadColors();

        // Enabele error reporting
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);

        // Set error handler and shutdown hook
        set_error_handler('\Synful\IO\IOFunctions::catchError');
        register_shutdown_function('\Synful\IO\IOFunctions::onShutDown');

        // Load the configuration into system
        if (! IOFunctions::loadConfig()) {
            exit(2);
        }

        self::initializeSql();

        // Parse CLI
        $cli_parser = new CLIParser();
        $cli_parser->parseCLI();

        global $argv;
        if (self::isCommandLineInterface() && count($argv) < 1) {
            IOFunctions::out(LogLevel::INFO, $cli_parser->getUsage(), true, false, false);
            exit(3);
        }

        // Run Pre Start up functions
        self::preStartUp();

        self::$controller = new Controller();

        self::$controller->generateMasterKey();

        IOFunctions::out(LogLevel::NOTE, 'Loading Request Handlers...');
        self::loadRequestHandlers();

        if (self::$config['system']['standalone']) {
            IOFunctions::out(LogLevel::NOTE, 'Running in standalone mode...');
            self::postStartUp();
            (new Standalone(self::$config))->initialize();
        } else {
            self::listenWeb();
        }
    }

    /**
     * Initializes MySQL.
     */
    public static function initializeSql()
    {
        if (count(self::$config['sql_databases']) > 0) {
            IOFunctions::out(LogLevel::NOTE, 'Loading databases...');
            self::loadSqlDatabases(self::$config['sql_databases']);
        } else {
            trigger_error(
                'Missing Synful database definition.'.
                'Match the \'main_database\' setting in config.ini to the correct database. '.
                'Default Synful database is for storing API Keys, Users and Permissions.',
                E_USER_ERROR
            );
            exit(1);
        }

        self::$sql = &self::$sql_databases[self::$config['system']['main_database']];

        self::createDefaultTables();
    }

    /**
     * Loads all MySQL Databases into Synful.
     */
    private static function loadSqlDatabases($databases)
    {
        foreach ($databases as $database) {
            $database = (array) json_decode(str_replace('\'', '"', $database));
            if (count($database) < 5) {
                IOFunctions::out(
                    LogLevel::ERRO,
                    'Failed one or more custom databases. Please check config.ini.'
                );
                exit(1);
            } else {
                $new_sql_connection = new SqlConnection(
                    $database[0],
                    $database[1],
                    $database[2],
                    $database[3],
                    $database[4]
                );
                if ($new_sql_connection->openSQL()) {
                    self::$sql_databases[$database[3]] = $new_sql_connection;
                } else {
                    IOFunctions::out(
                        LogLevel::ERRO,
                        'Failed on one or more database connections. Please check config.ini.'
                    );
                    exit(1);
                }
            }
        }
    }

    /**
     * Loads all request handlers stored in 'system/request_handlers' into system.
     */
    private static function loadRequestHandlers()
    {
        $enabled_request_handler = false;
        foreach (scandir('./src/Synful/RequestHandlers') as $handler) {
            if (substr($handler, 0, 1) !== '.' && $handler != 'Interfaces') {
                $enabled_request_handler = true;
                $class_name = explode('.', $handler)[0];
                eval(
                    '\\Synful\\Synful::$request_handlers[\''.
                    $class_name.'\'] = new \\Synful\\RequestHandlers\\'.
                    $class_name.'();'
                );
                $is_public = false;
                $is_private = false;
                if (self::$config['security']['allow_public_requests']) {
                    if (property_exists(self::$request_handlers[$class_name], 'is_public')) {
                        $is_public = self::$request_handlers[$class_name]->is_public;
                    } elseif (property_exists(self::$request_handlers[$class_name], 'white_list_keys')) {
                        if (is_array(self::$request_handlers[$class_name]->white_list_keys)) {
                            $is_private = true;
                        }
                    }
                }
                IOFunctions::out(
                    LogLevel::NOTE,
                    '    Loaded Request Handler: '.$class_name.
                    (($is_public)
                        ? Colors::cs(
                            ' (Public)',
                            'light_green'
                        )
                        : (($is_private)
                            ? Colors::cs(
                                ' (Private)',
                                'light_red'
                            )
                            : Colors::cs(
                                ' (Standard)',
                                'light_blue'
                            )
                        )
                    )
                );
            }
        }
        if (! $enabled_request_handler) {
            IOFunctions::out(
                LogLevel::WARN,
                'No request handlers found. '.
                'Use \'php synful.php createhandler=HandlerName\' to create a new handler.'
            );
            IOFunctions::out(
                LogLevel::WARN,
                'Note: Request handlers are case sensitive. '.
                'We recommend using TitleCase for request handler names.'
            );
        }
    }

    /**
     * Runs the API thread on the local web server and outputs it's response in JSON format.
     */
    private static function listenWeb()
    {
        if (empty($_POST['request'])) {
            $response = new Response();
            $response->code = 400;
            $response->setResponse('message', 'Bad Request');
        } else {
            $response = self::$controller->handleRequest($_POST['request'], self::getClientIP());
        }

        header('Content-Type: text/json');
        http_response_code($response->code);
        IOFunctions::out(LogLevel::RESP, json_encode($response), true, true);
    }

    /**
     * Retreives the client IP Address.
     *
     * @return string The ip of the remote client
     */
    private static function getClientIP()
    {
        $ipaddress = '';

        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * Check if this is a CLI instance.
     *
     * @return bool
     */
    public static function isCommandLineInterface()
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Create default Synful tables.
     *
     * @return bool
     */
    private static function createDefaultTables()
    {
        return
            self::$sql->executeSql(
                'CREATE TABLE IF NOT EXISTS `api_keys` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , '.
                '`name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `api_key` VARCHAR(255) NOT NULL , '.
                '`whitelist_only` INT NOT NULL , `is_master` INT NOT NULL, `enabled` INT NOT NULL , '.
                'PRIMARY KEY (`id`)) ENGINE = MyISAM;'
            )

            && self::$sql->executeSql(
                'CREATE TABLE IF NOT EXISTS `api_perms` ( `api_key_id` INT UNSIGNED NOT NULL , '.
                '`put_data` INT NOT NULL , `get_data` INT NOT NULL , `mod_data` INT NOT NULL , '.
                'PRIMARY KEY (`api_key_id`) ) ENGINE = MyISAM;'
            )

            && self::$sql->executeSql(
                'CREATE TABLE IF NOT EXISTS `ip_firewall` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT '.
                ', `api_key_id` INT UNSIGNED NOT NULL , `ip` VARCHAR(255) NOT NULL , `block` INT NOT NULL '.
                ', PRIMARY KEY (`id`) ) ENGINE = MyISAM;'
            );
    }

    /**
     * Function to be called after startup has been completed.
     */
    public static function postStartUp()
    {
        IOFunctions::out(LogLevel::NOTE, '---------------------------------------------------', false, false, false);
    }

    /**
     * Function to be called prior to start up running.
     */
    public static function preStartUp()
    {
        IOFunctions::out(LogLevel::NOTE, '---------------------------------------------------', false, false, false);
        IOFunctions::out(LogLevel::NOTE, 'Synful API Initializing...');
    }
}
