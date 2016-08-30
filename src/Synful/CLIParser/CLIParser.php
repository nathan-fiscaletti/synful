<?php
	
	namespace Synful\CLIParser;
	
	use Synful\IO\IOFunctions;
	use Synful\IO\LogLevel;

	class CLIParser {

		private $script_name = '';

		private $valid_arguments = [

			'standalone' => [
				'name' => 'standalone',
				'usage' => 'standalone | standalone=[true/false]',
				'description' => 'Tells the system to open a local socket instead of relying on a web server.'
			],

			'logfile' => [
				'name' => 'logfile',
				'usage' => 'logfile=[filename]',
				'description' => 'Define a different log file to store the system logs in.'
			],

			'ip' => [
				'name' => 'ip',
				'usage' => 'ip=[x.x.x.x]',
				'description' => 'Define a different IP for standalone server to run on.'
			],

			'port' => [
				'name' => 'port',
				'usage' => 'port=[0 - 65535]',
				'description' => 'Define a different port to run the standalone server on.'
			],

			'multithread' => [
				'name' => 'multithread',
				'usage' => 'multithread | multithread=[true/false]',
				'description' => 'Tells the system to use a multithread server. (Requires multithread support in PHP.)'
			],

			'createhandler' => [
				'name' => 'createhandler',
				'usage' => 'createhandler=name',
				'description' => 'Creates a request handler with the specified name in src/Synful/RequestHandlers'
			],

			'createkey' => [
				'name' => 'createkey',
				'usage' => 'createkey=<email>,<First_Last>',
				'description' => 'Creates a new API key with the specified information.'
			]

		];

		/**
		 * Parse the Command Line parameters for the PHP Script
		 */
		public function parseCLI(){

			global $argv;

			if(empty($argv)) return;

			$this->script_name = $argv[0];

			array_shift($argv);
			
			if(!$this->isValidCLI()){
				IOFunctions::out(LogLevel::INFO, $this->getUsage(), true);
				exit(3);
			}

			
			if(sizeof($argv) > 0)
				foreach($argv as $arg)
					$this->parseArgument($arg);

		}

		/**
		 * Retrievs a detailed string containing the CLI usage for the script
		 */
		private function getUsage(){

			$usage = "\r\n" . '    Usage: php ' . $this->script_name . " [arg1, arg2, ...]\r\n\r\n    Arguments:\r\n";
			foreach($this->valid_arguments as $argument){
				$usage .= '        ' . $argument['name'] . ' : ' . $argument['description'] . "\r\n"
				        . '             Argument Usage : ' . $argument['usage'] . "\r\n";

			}
			return $usage . "\r\n";

		}

		/**
		 * Parse a command line argument
		 * @param string $argument The command line argument to parse
		 */
		private function parseArgument($argument){
			foreach($this->valid_arguments as $valid_argument){
				$cli_data = explode('=', $argument);
				if($valid_argument['name'] == $cli_data[0]){
					call_user_func('\Synful\CLIParser\CLIHandlers::' . $valid_argument['name'], (sizeof($cli_data) > 1) ? $cli_data[1] : null);
					return;
				}
			}
		
		}

		/**
		 * Verify that all parameters passed to the script are valid
		 * @return boolean True if all parameters are valid
		 */
		private function isValidCLI(){
			
			global $argv;

			foreach($argv as $arg){
				if(!array_key_exists(explode('=', $arg)[0], $this->valid_arguments)){
					trigger_error('Unknown parameter: \'' . $arg . '\'', E_USER_WARNING);
					return false;
				}
			}

			return true;

		}
	}
?>