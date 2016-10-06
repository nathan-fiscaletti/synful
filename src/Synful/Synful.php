<?php

namespace Synful;

use Synful\DataManagement\Models\APIKey;
use Synful\DataManagement\SqlConnection;
use Synful\Standalone\Standalone;
use Synful\CLIParser\CLIParser;
use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;
use Synful\Util\Security\Encryption;
use Synful\Util\ASCII\Colors;
use Synful\Util\Framework\SynfulException;
use Synful\Util\Framework\Validator;
use Synful\Util\Framework\Response;

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

        // Load console color codes
        Colors::loadColors();

        // Enabele error reporting
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);

        // Set error handler and shutdown hook
        set_error_handler('\\Synful\\IO\\IOFunctions::catchError', E_ALL);
        register_shutdown_function('\\Synful\\IO\\IOFunctions::onShutDown');

        // Load the configuration into system
        if (! IOFunctions::loadConfig()) {
            exit();
        }

        // Load encryption
        if (sf_conf('security.use_encryption')) {
            self::$crypto = new Encryption([
                'key' => sf_conf('security.encryption_key'),
            ]);
        }

        self::initializeSql();

        // Parse CLI
        if (self::isCommandLineInterface()) {
            $cli_parser = new CLIParser();
            $cli_parser->parseCLI();
        }

        global $argv;
        if (self::isCommandLineInterface() && count($argv) < 1) {
            sf_info($cli_parser->getUsage(), true, false, false);
            exit(3);
        }

        // Run Pre Start up functions
        self::preStartUp();

        // Instatiate new validator
        self::$validator = new Validator();

        // Generate a new master API key if one does not exist
        APIKey::generateMasterKey();

        sf_note('Loading Request Handlers...');
        self::loadRequestHandlers();

        if (sf_conf('system.standalone')) {
            sf_note('Running in standalone mode...');
            self::postStartUp();
            (new Standalone())->initialize();
        } else {
            self::listenWeb();
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
                    trigger_error('Failed one or more custom databases. Please check SqlServers.php.', E_USER_WARNING);
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
                sf_note(
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
     * Passes a JSON Request through the desired request handlers, validates authentication
     * and request integrity and returns a response.
     *
     * @param  string                          $request
     * @param  string                          $ip
     * @return \Synful\Util\Framework\Response
     */
    public static function handleRequest($request, $ip)
    {
        $data = (array) json_decode($request);
        $response = new Response(['requesting_ip' => $ip]);

        try {
            if (self::$validator->validateRequest($data, $response) && self::$validator->validateHandler($data, $response)) {
                $handler = &Synful::$request_handlers[$data['handler']];
                $api_key = null;
                if (self::$validator->validateAuthentication($data, $response, $api_key, $handler, $ip)) {
                    $handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
                }
            }
        } catch (SynfulException $synfulException) {
            $response = $synfulException->response;
        }

        return $response;
    }

    /**
     * Runs the API thread on the local web server and outputs it's response in JSON format.
     */
    private static function listenWeb()
    {
        header('Content-Type: text/json');
        if (empty($_POST['request'])) {
            $response = (new SynfulException(null, 400, 1013))->response;
            if (sf_conf('security.use_encryption')) {
                sf_respond(sf_encrypt(json_encode($response)));
            } else {
                if (sf_conf('system.pretty_responses') || isset($_GET['pretty'])) {
                    sf_respond(json_encode($response, JSON_PRETTY_PRINT));
                } else {
                    sf_respond(json_encode($response));
                }
            }
        } else {
            if (sf_conf('security.use_encryption')) {
                $response = self::handleRequest(
                    self::$crypto->decrypt($_POST['request']),
                    self::getClientIP()
                );
                sf_respond(sf_encrypt(json_encode($response)));
            } else {
                $response = self::handleRequest($_POST['request'], self::getClientIP());
                if (sf_conf('system.pretty_responses') || isset($_GET['pretty'])) {
                    sf_respond(json_encode($response, JSON_PRETTY_PRINT));
                } else {
                    sf_respond(json_encode($response));
                }
            }
        }
    }

    /**
     * Generate a test form for submitting requests.
     */
    public static function testForm()
    {
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
