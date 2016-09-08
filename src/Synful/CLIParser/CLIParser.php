<?php

namespace Synful\CLIParser;

use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;
use Synful\Colors;

/**
 * Class used for handling CLI Parameteres.
 */
class CLIParser
{
    /**
     * Array of valid CLI arguemnts.
     *
     * @var array
     */
    private $valid_arguments = [

        // Main System

        'standalone' => [
            'name' => 'standalone',
            'usage' => 'standalone | standalone=[true/false]',
            'description' => 'Tells the system to open a local socket instead of relying on a web server.',
            'callback' => 'standAlone',
        ],

        'logfile' => [
            'name' => 'logfile',
            'usage' => 'logfile=[filename]',
            'description' => 'Define a different log file to store the system logs in.',
            'callback' => 'logFile',
        ],

        'ip' => [
            'name' => 'ip',
            'usage' => 'ip=[x.x.x.x]',
            'description' => 'Define a different IP for standalone server to run on.',
            'callback' => 'listenIp',
        ],

        'port' => [
            'name' => 'port',
            'usage' => 'port=[0 - 65535]',
            'description' => 'Define a different port to run the standalone server on.',
            'callback' => 'listenPort',
        ],

        'multithread' => [
            'name' => 'multithread',
            'usage' => 'multithread | multithread=[true/false]',
            'description' => 'Tells the system to use a multithread server. (Requires multithread support in PHP.)',
            'callback' => 'multiThread',
        ],

        'color' => [
            'name' => 'color',
            'usage' => 'color | color=[true/false]',
            'description' => 'Use to enable/disable console color at run time.',
            'callback' => 'enableColor',
        ],

        'createhandler' => [
            'name' => 'createhandler',
            'usage' => 'createhandler=name',
            'description' => 'Creates a request handler with the specified name in src/Synful/RequestHandlers',
            'callback' => 'createHandler',
        ],

        'createkey' => [
            'name' => 'createkey',
            'usage' => 'createkey=<email>,<First_Last>,<is_whitelist_only>',
            'description' => 'Creates a new API key with the specified information.',
            'callback' => 'createKey',
        ],

        'removekey' => [
                'name' => 'removekey',
                'usage' => 'removekey=<email/ID>',
                'description' => 'Removes a key from the System based on email or ID.',
                'callback' => 'removeKey',
         ],

         'disablekey' => [
            'name' => 'disablekey',
            'usage' => 'disablekey=<email/ID>',
            'description' => 'Disables a key (making it unable to be used) based on email or ID.',
            'callback' => 'disableKey',
         ],

         'enablekey' => [
            'name' => 'enablekey',
            'usage' => 'enablekey=<email/ID>',
            'description' => 'Enables a key that has been disabled based on email or ID.',
            'callback' => 'enableKey',
         ],

         'listkeys' => [
            'name' => 'listkeys',
            'usage' => 'listkeys',
            'description' => 'Outputs a list of all API Keys.',
            'callback' => 'listKeys',
         ],

         'firewallip' => [
            'name' => 'firewallip',
            'usage' => 'firewallip=<email/id>,<ip>,<block>',
            'description' => 'Firewalls an IP Address on the specified key with the specified block value',
            'callback' => 'fireWallIp',
         ],

         'unfirewallip' => [
            'name' => 'unfirewallip',
            'usage' => 'unfirewallip=<email/id>,<ip>',
            'description' => 'Removes the firewall entry for the specified ip on the specified key',
            'callback' => 'unFireWallIp',
         ],

         'showfirewall' => [
            'name' => 'showfirewall',
            'usage' => 'showfirewall=<email/id>',
            'description' => 'Lists firewall entries for a specific key.',
            'callback' => 'showFireWall',
         ],

         'whitelistonly' => [
            'name' => 'whitelistonly',
            'usage' => 'whitelistonly=<email,ID>,<true/false>',
            'description' => 'Enables or disables the \'White-List Only\' Option for the specified key.',
            'callback' => 'whiteListOnly',
         ],

    ];

    /**
     * The name of the script being executed.
     *
     * @var string
     */
    private $script_name = '';

    /**
     * Parse the Command Line parameters for the PHP Script.
     */
    public function parseCLI()
    {
        global $argv;

        if (! empty($argv)) {
            $this->script_name = $argv[0];
            array_shift($argv);

            if ($this->validateCLI()) {
                if (count($argv) > 0) {
                    foreach ($argv as $arg) {
                        $this->parseArgument($arg);
                    }
                }
            } else {
                IOFunctions::out(LogLevel::INFO, $this->getUsage(), false, false, false);
                exit(3);
            }
        } else {
            IOFunctions::out(LogLevel::INFO, $this->getUsage(), false, false, false);
            exit(3);
        }
    }

    /**
     * Retrievs a detailed string containing the CLI usage for the script.
     */
    public function getUsage()
    {
        $usage = PHP_EOL.'    Usage: php ';
        $usage .= $this->script_name.' [arg1, arg2,...]'.PHP_EOL;
        $usage .= '    <> = Denotes a required part of usage.'.PHP_EOL;
        $usage .= '    [] = Denotes an optional part of usage.'.PHP_EOL;
        $usage .= PHP_EOL.'    Arguments:'.PHP_EOL;

        $largest_argument = max(array_map('strlen', array_keys($this->valid_arguments)));

        foreach ($this->valid_arguments as $argument) {
            $usage .= str_pad('', 8);
            $usage .= Colors::cs(str_pad($argument['name'], $largest_argument), 'light_cyan');
            $usage .= ' : '.Colors::cs($argument['description'], 'yellow').PHP_EOL;
            $usage .= str_pad('Argument Usage : ', 29, ' ', STR_PAD_LEFT);
            $usage .= $argument['usage'].PHP_EOL.PHP_EOL;

            $spaces = '';
        }

        return $usage.PHP_EOL;
    }

    /**
     * Parse a command line argument.
     *
     * @param string $argument
     */
    private function parseArgument($argument)
    {
        foreach ($this->valid_arguments as $valid_argument) {
            $cli_data = explode('=', $argument);
            if ($valid_argument['name'] == $cli_data[0]) {
                call_user_func(
                    '\Synful\CLIParser\CLIHandlers::'.$valid_argument['callback'],
                    (count($cli_data) > 1) ? $cli_data[1] : null
                );
                break;
            }
        }
    }

    /**
     * Verify that all parameters passed to the script are valid.
     *
     * @return bool
     */
    private function validateCLI()
    {
        global $argv;
        $ret = true;

        foreach ($argv as $arg) {
            if (! array_key_exists(explode('=', $arg)[0], $this->valid_arguments)) {
                trigger_error('Unknown parameter: \''.$arg.'\'', E_USER_WARNING);
                $ret = false;
                break;
            }
        }

        return $ret;
    }
}
