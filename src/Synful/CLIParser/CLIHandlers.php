<?php

	namespace Synful\CLIParser;

	use Synful\Synful;
	use Synful\IO\IOFunctions;
	use Synful\IO\LogLevel;
	
	/**
	 * Store handlers for CLI Parameters in this file
	 * I declare this file PSR-2 exempt
	 */

	class CLIHandlers {

		/**
		 * Handle createhandler CLI Parameter
		 * @param  String $value The value set in CLI
		 */
		public static function createhandler($value){
			$value = str_replace(' ', '', $value);
			$value = str_replace('_', '', $value);
			$value = trim($value);

			if(!ctype_alpha($value)){
				IOFunctions::out(LogLevel::ERRO, 'Error: Request Handler names must only contain alphabetic characters and no spaces. Title case recommended -> (ThisIsTitleCase).', true);
				exit(0);
			}else{
				if(!file_exists('./src/Synful/RequestHandlers/' . $value . '.php')){
					file_put_contents('./src/Synful/RequestHandlers/' . $value . '.php', "<?php\r\n\r\n    namespace Synful\RequestHandlers;\r\n\r\n    use Synful\Synful;\r\n    use Synful\RequestHandlers\Abstraction\RequestHandler;\r\n    use Synful\Response;\r\n\r\n    class " . $value . " extends RequestHandler {\r\n\r\n    	 /**\r\n    	 * Change this to 'true' if you want your request handler to be publically accessible\r\n    	 * Note: When set to 'true', this request handler will not require an API Key\r\n    	 */\r\n    	public function __construct(){\r\n    		". '$this->is_public' . " = false;\r\n    	}\r\n\r\n		/**\r\n		 * Function for handling request and returning data as a Response object\r\n		 * @param  Response " . '$data' . "              The data received by reference\r\n		 * @param  boolean  " . '$is_master_request' . " True if the key being used to access the request is a master key\r\n		 */\r\n		public function handleRequest(Response &" . '$data' . ", " . '$is_master_request' . " = false){\r\n			" . '$request_data =& $data->request;' . "\r\n\r\n			// Insert your code here\r\n\r\n		}\r\n\r\n	}\r\n\r\n?>\r\n");
						IOFunctions::out(LogLevel::INFO, 'Created Request Handler in \'src/Synful/RequestHandlers\' with name \'' . $value . '\'.', true);
						chmod('./src/Synful/RequestHandlers/' . $value . '.php', 0700);
						exec('chmod +x ./src/Synful/RequestHandlers/' . $value . '.php');
						exec('php composer.phar dumpautoload');
						exit(0);
				}else{
					IOFunctions::out(LogLevel::ERRO, 'Error: A request handler by that name already exists.', true);
					exit(0);
				}
			}
		}

		/**
		 * Handle standalone CLI Parameter
		 * @param boolean $value The value set in CLI
		 */
		public static function standalone($value){
			Synful::$config['system']['standalone'] = ($value == null) ? true : json_decode($value);
			IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set standalone mode to \'true\'.');
		}

		/**
		 * Handle logfile CLI Parameter
		 * @param string $value The value set in CLI
		 */
		public static function logfile($value){

			if($value != null){
				Synful::$config['files']['logfile'] = $value;
				IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set logfile to \'' . $value . '\'.');
			}

			else
				IOFunctions::out(LogLevel::WARN, 'Invalid logfile defined. Using default.');

		}

		/**
		 * Handle ip CLI Parameter
		 * @param string $value The value set in CLI
		 */
		public static function ip($value){
			if($value != null){
				if (!filter_var($ip, FILTER_VALIDATE_IP) === false){
					Synful::$config['system']['ip'] = $value;
					IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set IP to \'' . $value . '\'.');
				}

				else
					IOFunctions::out(LogLevel::WARN, 'Invalid IP defined. Using default.');	

			}

			else
				IOFunctions::out(LogLevel::WARN, 'Invalid IP defined. Using default.');
		}

		/**
		 * Handle port CLI Parameter
		 * @param integer $value The value set in CLI
		 */
		public static function port($value){
			if($value != null){
				Synful::$config['system']['port'] = $value;
				IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set port to \'' . $value . '\'.');
			}

			else
				IOFunctions::out(LogLevel::WARN, 'Invalid port defined. Using default.');

		}

		/**
		 * Handle multithread CLI Parameter
		 * @param boolean $value The value set in CLI
		 */
		public static function multithread($value){
			Synful::$config['system']['multithread'] = ($value == null) ? true : json_decode($value);
			IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set multithread mode to \'true\'.');
		}

		

	}
?>