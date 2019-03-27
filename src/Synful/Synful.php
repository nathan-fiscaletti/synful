<?php

namespace Synful;

use Synful\ASCII\Colors;
use Synful\Data\Database;
use Synful\IO\IOFunctions;
use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\RateLimit;
use Synful\CLIParser\CommandLine;
use Synful\WebListener\WebListener;
use Synful\Framework\RequestHandler;
use Synful\Framework\SynfulException;

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
     * All request handlers registered in the system.
     *
     * @var array
     */
    public static $request_handlers = [];

    /**
     * The result of the command line parsing.
     *
     * @var array
     */
    public static $command_results = [];

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
        set_error_handler('\\Synful\\IO\\IOFunctions::catchError', E_ALL);
        register_shutdown_function('\\Synful\\IO\\IOFunctions::onShutDown');

        // Check Cross Origin Resource Sharing
        if (sf_conf('system.cors_enabled')) {
            if (in_array('all', sf_conf('system.cors_domains'))) {
                header('Access-Control-Allow-Origin: *');
            } else {
                foreach (sf_conf('system.cors_domains') as $domain) {
                    if ($_SERVER['HTTP_ORIGIN'] == $domain) {
                        header('Access-Control-Allow-Origin: '.$domain);
                        break;
                    }
                }
            }
        }

        // Check global rate limiter
        if (sf_conf('rate.global')) {
            if (! RateLimit::global()->isUnlimited()) {
                if (RateLimit::global()->isLimited(self::getClientIP())) {
                    $response = (new SynfulException(500, 1028))->response;
                    sf_respond($response->code, $response->serialize());
                    exit;
                }
            }
        }

        // Initialize the Database Connections
        self::initializeDatabases();

        // Load request handlers
        self::loadRequestHandlers();

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

            self::$command_results = $results;
        }

        // Initialize WebListener
        (new WebListener())->initialize();
    }

    /**
     * Initializes Database Connection.
     */
    public static function initializeDatabases()
    {
        if (sf_conf('databases.synful') != null) {
            Database::initialize(sf_conf('databases'));
        } else {
            trigger_error(
                'Missing Synful database definition. '.
                'Set \'synful\' in \'config/Databases.php\'. '.
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
     * @param  \Synful\Framework\RequestHandler $handler
     * @param  string                                $input
     * @param  array                                 $fields
     * @param  string                                $ip
     * @return \Synful\Framework\Response
     */
    public static function handleRequest(
        RequestHandler $handler,
        string $input,
        array  $fields,
        string $ip
    ) {
        // Check rate limit for RequestHandler
        if (sf_conf('rate.per_handler')) {
            if (
                property_exists($handler, 'rate_limit') &&
                is_array($handler->rate_limit) &&
                isset($handler->rate_limit['requests']) &&
                isset($handler->rate_limit['per_seconds']) &&
                is_int($handler->rate_limit['requests']) &&
                is_int($handler->rate_limit['per_seconds'])
            ) {
                $rh_rl = new RateLimit(
                    'handler_'.$handler->endpoint,
                    $handler->rate_limit['requests'],
                    $handler->rate_limit['per_seconds']
                );
                if (! $rh_rl->isUnlimited()) {
                    if ($rh_rl->isLimited(self::getClientIP())) {
                        $response = (new SynfulException(500, 1029))->response;
                        sf_respond($response->code, $response->serialize());
                        exit;
                    }
                }
            }
        }

        $serializer = sf_conf('system.serializer');
        $serializer = new $serializer;

        if (property_exists($handler, 'serializer')) {
            $serializer = new $handler->serializer;
        }

        if (! empty($input)) {
            try {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $data = (new \Synful\Serializers\URLSerializer)->deserialize($input);
                } else {
                    $data = $serializer->deserialize($input);
                }
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
            'headers' => sf_headers(),
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
     * Retrieve the current version of the framework from git.
     *
     * @return string
     */
    public static function version()
    {
        if (! file_exists('.git')) {
            return '*no-git* (dirty)';
        }

        return preg_replace('/\s+/', '', shell_exec('git describe --tag'));
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
            self::$request_handlers[$requestHandlerClass] = new $requestHandlerClass();
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
        foreach (scandir('./src/Synful/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                include_once './src/Synful/Functions/'.$func_lib;
            }
        }

        foreach (scandir('./src/App/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                include_once './src/App/Functions/'.$func_lib;
            }
        }
    }
}
