<?php

namespace Synful;

use Exception;
use Gestalt\Configuration;
use Spackle\Plugin;
use Synful\Data\Database;
use Synful\IO\IOFunctions;
use Synful\Framework\Route;
use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\RateLimit;
use Synful\Command\CommandLine;
use Synful\Serializers\URLSerializer;
use Synful\WebListener\WebListener;
use Synful\Framework\SynfulException;
use Synful\Serializers\HtmlSerializer;

use Spackle\TemplateParser as Template;

/**
 * Primary class for framework.
 */
class Synful
{
    /**
     * The config for the system pulled from './config/'.
     *
     * @var Configuration
     */
    public static $config;

    /**
     * All Routes registered in the system.
     *
     * @var array
     */
    public static $routes = [];

    /**
     * The result of the command line parsing.
     *
     * @var array
     */
    public static $command_results = [];

    /**
     * Initialize the Synful API instance.
     * @throws SynfulException
     * @throws Exception
     */
    public static function initialize()
    {
        // Load Global Functions
        self::loadGlobalFunctions();

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

        // Load Template Plugins
        if (sf_conf('templating.enabled')) {
            self::loadTemplatePlugins();
        }

        // Initialize the Database Connections
        self::initializeDatabases();

        // Load routes
        self::loadRoutes();

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
     * Passes a JSON Request through the desired route, validates authentication
     * and request integrity and returns a response.
     *
     * @param Route $route
     * @param string $input
     * @param array $fields
     * @param string $ip
     *
     * @return Response
     * @throws SynfulException
     */
    public static function handleRequest(
        Route  $route,
        string $input,
        array  $fields,
        string $ip
    ) {
        // Check rate limit for Route
        if (sf_conf('rate.per_route')) {
            if (
                property_exists($route, 'rate_limit') &&
                is_array($route->rate_limit) &&
                isset($route->rate_limit['requests']) &&
                isset($route->rate_limit['per_seconds']) &&
                is_int($route->rate_limit['requests']) &&
                is_int($route->rate_limit['per_seconds'])
            ) {
                /** @noinspection PhpUndefinedFieldInspection */
                $rh_rl = new RateLimit(
                    'synful_route_'.$route->endpoint,
                    $route->rate_limit['requests'],
                    $route->rate_limit['per_seconds']
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

        if (
            property_exists($route, 'serializer') &&
            class_exists($route->serializer)
        ) {
            $serializer = new $route->serializer;
        }

        if (! empty($input)) {
            try {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $data = (new URLSerializer)->deserialize($input);
                } else {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $data = $serializer->deserialize($input);
                }
            } catch (Exception $e) {
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
            'route' => $route,
        ]);

        try {
            $middlewares = [];

            if (! class_exists($route->controller)) {
                trigger_error(
                    'Unknown controller defined in route: Attempted '.
                    $route->controller,
                    E_USER_ERROR
                );
                exit;
            }

            if (! method_exists($route->controller, $route->call)) {
                trigger_error(
                    'Unknown controller call defined in route: Attempted '.
                    $route->controller.'@'.$route->call,
                    E_USER_ERROR
                );
                exit;
            }

            if (property_exists($route, 'middleware')) {
                if (! is_array($route->middleware)) {
                    throw new SynfulException(500, 1017);
                }

                $middlewares = $route->middleware;
            }

            foreach ($middlewares as $middleware) {
                $middleware = new $middleware;
                /** @noinspection PhpUndefinedMethodInspection */
                $middleware->before($request, $route);
            }

            $request->route = $route;
            $controller = $route->controller;
            $call = $route->call;
            $response = (new $controller)->{$call}($request);

            if (is_array($response)) {
                $response = sf_response(200, $response);
            }

            if (sf_conf('templating.enabled')) {
                if ($response instanceof Template)
                {
                    return sf_response(
                        200,
                        [
                            'html' => $response->parse()
                        ]
                    )->setSerializer(
                        new HtmlSerializer()
                    );
                }
            }

            if (! ($response instanceof Response)) {
                throw new SynfulException(500, 1016);
            }
        } catch (SynfulException $synfulException) {
            $response = $synfulException->response;
        }

        $response->request = $request;
        return $response;
    }

    /**
     * Load Template Plugins.
     * @throws Exception
     */
    public static function loadTemplatePlugins()
    {
        foreach (sf_conf('templating.plugins') as $plugin) {
            Plugin::add(
                new $plugin
            );
        }
    }

    /**
     * Retreives the client IP Address.
     *
     * @return string The ip of the remote client
     */
    public static function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip_address = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip_address = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip_address = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip_address = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip_address = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip_address = getenv('REMOTE_ADDR');
        } else {
            $ip_address = 'UNKNOWN';
        }

        return $ip_address;
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
     * Load all routes configured in routes.yaml.
     * @throws SynfulException
     */
    private static function loadRoutes()
    {
        $route_found = false;
        foreach (
            sf_conf('routes') as $path => $route_data
        ) {
            $route_found = true;
            self::$routes[$path] = Route::buildRoute($path, $route_data);
        }

        if (! $route_found) {
            trigger_error(
                'No routes have been configured. Check routes.yaml.',
                E_USER_ERROR
            );

            exit;
        }
    }

    /**
     * Automatically include all function libraries stored in Global Functions.
     * Note: This must use scandir as it is loaded before the configs.
     */
    public static function loadGlobalFunctions()
    {
        foreach (scandir('./src/Synful/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                /** @noinspection PhpIncludeInspection */
                include_once './src/Synful/Functions/'.$func_lib;
            }
        }

        foreach (scandir('./src/App/Functions') as $func_lib) {
            if (substr($func_lib, 0, 1) !== '.') {
                /** @noinspection PhpIncludeInspection */
                include_once './src/App/Functions/'.$func_lib;
            }
        }
    }
}
