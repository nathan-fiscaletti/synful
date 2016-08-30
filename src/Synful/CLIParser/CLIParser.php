<?php
	
	namespace Synful\CLIParser;
	
	use Synful\IO\IOFunctions;
	use Synful\IO\LogLevel;

	class CLIParser {

		private $script_name = '';

		private $valid_arguments = [

			// Main System

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

			'color' => [
				'name' => 'color',
				'usage' => 'color | color=[true/false]',
				'description' => 'Use to enable/disable console color at run time.'
			],

			// Handler Management

			'createhandler' => [
				'name' => 'createhandler',
				'usage' => 'createhandler=name',
				'description' => 'Creates a request handler with the specified name in src/Synful/RequestHandlers'
			],

			// Key Management

			'createkey' => [
				'name' => 'createkey',
				'usage' => 'createkey=<email>,<First_Last>,<is_whitelist_only>',
				'description' => 'Creates a new API key with the specified information.'
			],

			'removekey' => [
  				'name' => 'removekey',
  				'usage' => 'removekey=<email/ID>',
  				'description' => 'Removes a key from the System based on email or ID.'
			 ],

			 'disablekey' => [
			 	'name' => 'disablekey',
			 	'usage' => 'disablekey=<email/ID>',
			 	'description' => 'Disables a key (making it unable to be used) based on email or ID.'
			 ],

			 'enablekey' => [
			 	'name' => 'enablekey',
			 	'usage' => 'enablekey=<email/ID>',
			 	'description' => 'Enables a key that has been disabled based on email or ID.'
			 ],

			 'listkeys' => [
			 	'name' => 'listkeys',
			 	'usage' => 'listkeys',
			 	'description' => 'Outputs a list of all API Keys.'
			 ],

			 // Firewall management
			 
			 'firewallip' => [
			 	'name' => 'firewallip',
			 	'usage' => 'firewallip=<email/id>,<ip>,<block>',
			 	'description' => 'Firewalls an IP Address on the specified key with the specified block value'
			 ],

			 'unfirewallip' => [
			 	'name' => 'unfirewallip',
			 	'usage' => 'unfirewallip=<email/id>,<ip>',
			 	'description' => 'Removes the firewall entry for the specified ip on the specified key'
			 ],

			 'showfirewall' => [
			 	'name' => 'showfirewall',
			 	'usage' => 'showfirewall=<email/id>',
			 	'description' => 'Lists firewall entries for a specific key.'
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
			
			if(!$this->validateCLI()){
				IOFunctions::out(LogLevel::INFO, $this->getUsage(), true, false, false);
				exit(3);
			}

			
			if(sizeof($argv) > 0)
				foreach($argv as $arg)
					$this->parseArgument($arg);

		}

		/**
		 * Retrievs a detailed string containing the CLI usage for the script
		 */
		public function getUsage(){

			$usage = "\r\n" . '    Usage: php ' . $this->script_name . " [arg1, arg2, ...]\r\n\r\n    Arguments:\r\n";
			
			foreach ($this->valid_arguments as $argument) {
				
				$usage .= '        ' . str_pad($argument['name'], max(array_map('strlen', array_keys($this->valid_arguments)))) . ' : ' . $argument['description'] . "\r\n"
				        . '             Argument Usage : ' . $argument['usage'] . "\r\n\r\n";

				$spaces = '';

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
		private function validateCLI(){
			
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