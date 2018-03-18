<?php

namespace Synful;

use Synful\Util\ASCII\Colors;
use Synful\Util\IO\IOFunctions;
use Synful\Util\Framework\Request;
use Synful\Util\Framework\Response;
use Synful\Util\CLIParser\CommandLine;
use Synful\Util\WebListener\WebListener;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\Framework\SynfulException;
use Synful\Util\DataManagement\SqlConnection;

/**
 * Primary class for framework.
 */
class Synful
{
    /**
     * The config for the system pulled from './config/'.
     *
     * @var array
     */
    public static $config;

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
     * Initialize the Synful API instance.
     */
    public static function initialize()
    {
        // Make sure we aren't using that pesky PHP < 7.0
        0 <=> 0;

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

        // Initialize the Sql databases
        // This does not initialize a connection.
        self::initializeSql();

        // Parse Command Line
        if (self::isCommandLineInterface()) {
            global $argv;
            $commandLine = new CommandLine();
            $results = $commandLine->parse($argv);

            // Output Results
            if ((array_key_exists('hc', $results) && ! $results['hc']) ||
                ! array_key_exists('hc', $results)) {
                if (array_key_exists('cl', $results)) {
                    $str = (sf_conf('system.color')) ? 'true' : 'false';
                    sf_note('CONFIG: Set console color to \''.$str.'\'.');
                }

                if (array_key_exists('o', $results)) {
                    sf_note('CONFIG: Set output level to \''.$results['o'].'\'.');
                }
            }

            if ((count($argv) < 2 || substr($argv[1], 0, 7) == '-output' ||
                 substr($argv[1], 0, 2) == '-o')) {
                $commandLine->printUsage();
                exit(3);
            }
        }

        // Load request handlers
        self::loadRequestHandlers();

        // Initialize WebListener
        (new WebListener())->initialize();
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
                'Default Synful database is for storing API Keys.',
                E_USER_WARNING
            );
            exit();
        }
    }

    /**
     * Passes a JSON Request through the desired request handlers, validates authentication
     * and request integrity and returns a response.
     *
     * @param  \Synful\Util\Framework\RequestHandler $handler
     * @param  string                                $input
     * @param  array                                 $fields
     * @param  string                                $ip
     * @return \Synful\Util\Framework\Response
     */
    public static function handleRequest(
        RequestHandler $handler,
        string $input,
        array  $fields,
        string $ip
    ) {
        $serializer = sf_conf('system.serializer');
        $serializer = new $serializer;

        if (property_exists($handler, 'serializer')) {
            $serializer = new $handler->serializer;
        }

        if (! empty($input)) {
            try {
                $data = $serializer->deserialize($input);
            } catch (\Exception $e) {
                $response = (new SynfulException(500, -1, $e->getMessage()))->response;
                sf_respond($response->code, $response->serialize());
                exit;
            }
        } else {
            $data = [];
        }

        $request = new Request([
            'ip' => $ip,
            'headers' => apache_request_headers(),
            'data' => $data,
            'fields' => $fields,
            'method' => $_SERVER['REQUEST_METHOD'],
        ]);

        try {
            $all_middleware = sf_conf(
                'system.global_middleware'
            );

            if (property_exists($handler, 'middleware')) {
                if (! is_array($handler->middleware)) {
                    throw new SynfulException(500, 1017);
                }

                $all_middleware = $all_middleware + $handler->middleware;
            }

            foreach ($all_middleware as $middleware) {
                $middleware = new $middleware;
                $middleware->before($request, $handler);
            }

            $response = $handler->handleRequest($request);

            if (is_array($response)) {
                $response = sf_response(200, $response);
            }

            if (! ($response instanceof Response)) {
                throw new SynfulException(500, 1016);
            }
        } catch (SynfulException $synfulException) {
            $response = $synfulException->response;
        }

        return $response;
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
    public static function createDefaultTables()
    {
        return
            sf_sql(
                'CREATE TABLE IF NOT EXISTS `api_keys` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , '.
                '`name` VARCHAR(255) NOT NULL , `auth` VARCHAR(255) NOT NULL , `api_key` VARCHAR(255) NOT NULL , '.
                '`whitelist_only` INT NOT NULL , `security_level` INT NOT NULL, `enabled` INT NOT NULL , '.
                'PRIMARY KEY (`id`)) ENGINE = MyISAM;'
            )

            && sf_sql(
                'CREATE TABLE IF NOT EXISTS `ip_firewall` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT '.
                ', `api_key_id` INT UNSIGNED NOT NULL , `ip` VARCHAR(255) NOT NULL , `block` INT NOT NULL '.
                ', PRIMARY KEY (`id`) ) ENGINE = MyISAM;'
            );
    }

    /**
     * Loads all MySQL Databases into Synful.
     */
    private static function loadSqlServers($sqlservers)
    {
        foreach ($sqlservers as $server_name => $server) {
            foreach ($server['databases'] as $database_name => $database) {
                $new_sql_connection = new SqlConnection(
                    $server['host'],
                    $database['username'],
                    $database['password'],
                    $database['database'],
                    $server['port']
                );

                self::$sql_databases[$server_name.'.'.$database_name] = $new_sql_connection;
            }
        }
    }

    /**
     * Loads all request handlers stored in 'system/request_handlers' into system.
     */
    private static function loadRequestHandlers()
    {
        $enabled_request_handler = false;
        foreach (
            sf_conf('requesthandlers.registered') as $requestHandlerClass
        ) {
            $enabled_request_handler = true;
            Synful::$request_handlers[$requestHandlerClass] = new $requestHandlerClass();
        }
        if (! $enabled_request_handler) {
            trigger_error(
                'No request handlers found.',
                E_USER_ERROR
            );

            exit;
        }
    }

    /**
     * Automatically include all function libraries stored in Global Functions.
     * Note: This must use scandir as it is loaded before the configs.
     */
    private static function loadGlobalFunctions()
    {
        foreach (scandir('./src/Synful/Util/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                include_once './src/Synful/Util/Functions/'.$func_lib;
            }
        }
    }
}
