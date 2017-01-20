<?php

namespace Synful;

use Synful\Util\ASCII\Colors;
use Synful\Util\IO\IOFunctions;
use Synful\Util\Framework\Response;
use Synful\Util\Framework\Validator;
use Synful\Util\Security\Encryption;
use Synful\Util\CLIParser\CommandLine;
use Synful\Util\Standalone\Standalone;
use Synful\Util\WebListener\WebListener;
use Synful\Util\Framework\SynfulException;
use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\DataManagement\SqlConnection;

/**
 * Primary class for framework.
 */
class Synful
{
    /**
     * The config for the system pulled from './config/Main.php'.
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
     * The primary validator.
     *
     * @var Synful\Util\Framework\Validator
     */
    public static $validator;

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
     * The encrpytion object used by Synful.
     *
     * @var Synful\Util\Security\Encryption
     */
    public static $crypto;

    /**
     * Initialize the Synful API instance using either Standalone Mode
     * or Local Web Server.
     */
    public static function initialize()
    {
        // Make sure we aren't using that pesky PHP < 7.0
        0 <=> 0;

        // Set content type
        header('Content-Type: text/json');

        // Load Global Functions
        self::loadGlobalFunctions();

        // Load console color codes
        Colors::loadColors();

        // Load the configuration into system
        if (! IOFunctions::loadConfig()) {
            exit();
        }

        // Enabele error reporting
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);
        if (sf_conf('system.display_errors')) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        // Set error handler and shutdown hook
        set_error_handler('\\Synful\\Util\\IO\\IOFunctions::catchError', E_ALL);
        register_shutdown_function('\\Synful\\Util\\IO\\IOFunctions::onShutDown');

        // Load encryption
        self::$crypto = new Encryption([
            'key' => sf_conf('security.encryption_key'),
        ]);

        self::initializeSql();

        // Parse Command Line
        if (self::isCommandLineInterface()) {
            global $argv;
            $commandLine = new CommandLine();
            $results = $commandLine->parse($argv);

            // Output Results
            if ((array_key_exists('hc', $results) && !$results['hc']) ||
                ! array_key_exists('hc', $results)) {
                if (array_key_exists('cl', $results)) {
                    $str = (sf_conf('system.color')) ? 'true' : 'false';
                    sf_note('CONFIG: Set console color to \''.$str.'\'.');
                }

                if (array_key_exists('s', $results)) {
                    $str = (sf_conf('system.standalone')) ? 'true' : 'false';
                    sf_note('CONFIG: Set standalone mode to \''.$str.'\'.');
                }

                if (array_key_exists('o', $results)) {
                    sf_note('CONFIG: Set output level to \''.$results['o'].'\'.');
                }
            }

            if ((count($argv) < 2 || substr($argv[1], 0, 7) == '-output' ||
                 substr($argv[1], 0, 2) == '-o') && ! sf_conf('system.standalone')) {
                $commandLine->printUsage();
                exit(3);
            }
        }

        // Run Pre Start up functions
        self::preStartUp();

        // Instatiate new validator
        self::$validator = new Validator();

        // Generate a new master API key if one does not exist
        APIKey::generateMasterKey();
        if (self::isCommandLineInterface()) {
            sf_note('Loading Request Handlers...', true, false, false);
        }
        self::loadRequestHandlers();

        if (sf_conf('system.standalone')) {
            sf_note('Running in standalone mode...');
            self::postStartUp();
            (new Standalone())->initialize();
        } else {
            (new WebListener())->initialize();
        }
    }

    /**
     * Initializes MySQL.
     */
    // TODO - IMPORTANT
    public static function initializeSql()
    {
        if (sf_conf('sqlservers.main.databases.synful') != null) {
            self::loadSqlServers(sf_conf('sqlservers'));
        } else {
            trigger_error(
                'Missing Synful database definition. '.
                'Set \'sqlservers.main.databases.synful\' in \'SqlServers.php\'. '.
                'Default Synful database is for storing API Keys, Users and Permissions.',
                E_USER_WARNING
            );
            exit();
        }
        self::$sql = &self::$sql_databases['main.synful'];
        self::createDefaultTables();
    }

    /**
     * Loads all MySQL Databases into Synful.
     */
    private static function loadSqlServers($sqlservers)
    {
        foreach ($sqlservers as $server_name => $server) {
            foreach ($server['databases'] as $database_name => $database) {
                try {
                    $new_sql_connection = new SqlConnection(
                        $server['host'],
                        $database['username'],
                        $database['password'],
                        $database['database'],
                        $server['port']
                    );

                    if ($new_sql_connection->openSQL()) {
                        self::$sql_databases[$server_name.'.'.$database_name] = $new_sql_connection;
                    } else {
                        trigger_error(
                            'Failed one or more custom databases. Please check SqlServers.php.',
                            E_USER_WARNING
                        );
                        exit();
                    }
                } catch (Exception $e) {
                    trigger_error(
                        'Failed one or more custom databases. Please check SqlServers.php.',
                        E_USER_WARNING
                    );
                    exit();
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
                if (sf_conf('security.allow_public_requests')) {
                    if (property_exists(self::$request_handlers[$class_name], 'is_public')) {
                        $is_public = self::$request_handlers[$class_name]->is_public;
                    } elseif (property_exists(self::$request_handlers[$class_name], 'white_list_keys')) {
                        if (is_array(self::$request_handlers[$class_name]->white_list_keys)) {
                            $is_private = true;
                        }
                    }
                }
                if (self::isCommandLineInterface()) {
                    sf_note(
                        '    Loaded Request Handler: '.$class_name.
                        (($is_public)
                            ? sf_color(
                                ' (Public)',
                                'light_green'
                            )
                            : (($is_private)
                                ? sf_color(
                                    ' (Private)',
                                    'light_red'
                                )
                                : sf_color(
                                    ' (Standard)',
                                    'light_blue'
                                )
                            )
                        ),
                        true,
                        false,
                        false
                    );
                }
            }
        }
        if (! $enabled_request_handler) {
            sf_warn(
                'No request handlers found. '.
                'Use \'php synful.php createhandler=HandlerName\' to create a new handler.'
            );
            sf_warn(
                'Note: Request handlers are case sensitive. '.
                'We recommend using TitleCase for request handler names.'
            );
        }
    }

    /**
     * Automatically include all function libraries stored in Global Functions.
     */
    private static function loadGlobalFunctions()
    {
        foreach (scandir('./src/Synful/Util/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                include_once './src/Synful/Util/Functions/'.$func_lib;
            }
        }
    }

    /**
     * Passes a JSON Request through the desired request handlers, validates authentication
     * and request integrity and returns a response.
     *
     * @param  string                          $request
     * @param  string                          $ip
     * @param  bool                            $wasEncrypted
     * @return \Synful\Util\Framework\Response
     */
    public static function handleRequest($request, $ip, $wasEncrypted = false)
    {
        $data = (array) json_decode($request);
        $response = new Response(['requesting_ip' => $ip]);

        try {
            if (self::$validator->validateRequest($data, $response) &&
                self::$validator->validateHandler($data, $response)) {
                $handler = &self::$request_handlers[$data['handler']];
                $api_key = null;
                if (self::$validator->validateAuthentication($data, $response, $api_key, $handler, $ip)) {
                    if (property_exists($handler, 'encrypted_only')) {
                        if (($handler->encrypted_only && $wasEncrypted) || ! $handler->encrypted_only) {
                            $handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
                        } else {
                            throw new SynfulException($response, 400, 1014);
                        }
                    } else {
                        $handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
                    }
                }
            }
        } catch (SynfulException $synfulException) {
            $response = $synfulException->response;
        }

        return $response;
    }

    /**
     * Generate a test form for submitting requests.
     */
    public static function testForm()
    {
        // Load Global Functions
        self::loadGlobalFunctions();

        // Load the configuration into system
        if (IOFunctions::loadConfig() && ! sf_conf('system.production')) {
            readfile('./templates/TestForm.tmpl');
        } else {
            header('Location: /');
            exit;
        }
    }

    /**
     * Retreives the client IP Address.
     *
     * @return string The ip of the remote client
     */
    public static function getClientIP()
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
            sf_sql(
                'CREATE TABLE IF NOT EXISTS `api_keys` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , '.
                '`name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `api_key` VARCHAR(255) NOT NULL , '.
                '`whitelist_only` INT NOT NULL , `is_master` INT NOT NULL, `enabled` INT NOT NULL , '.
                'PRIMARY KEY (`id`)) ENGINE = MyISAM;'
            )

            && sf_sql(
                'CREATE TABLE IF NOT EXISTS `api_perms` ( `api_key_id` INT UNSIGNED NOT NULL , '.
                '`put_data` INT NOT NULL , `get_data` INT NOT NULL , `mod_data` INT NOT NULL , '.
                'PRIMARY KEY (`api_key_id`) ) ENGINE = MyISAM;'
            )

            && sf_sql(
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
        sf_note('---------------------------------------------------', false, false, false);
    }

    /**
     * Function to be called prior to start up running.
     */
    public static function preStartUp()
    {
    }
}
