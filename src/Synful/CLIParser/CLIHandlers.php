<?php

	namespace Synful\CLIParser;

	use Synful\Synful;
	use Synful\IO\IOFunctions;
	use Synful\IO\LogLevel;
	use Synful\DataManagement\Models\APIKey;
	
	/**
	 * Store handlers for CLI Parameters in this file
	 * I declare this file PSR-2 exempt
	 */

	class CLIHandlers {

		/**
		 * Handle createkey CLI Parameter
		 * @param  String $value The value set in CLI
		 */
		public static function createkey($value){

			$new_key_data = explode(',', $value);
			if(sizeof($new_key_data) < 3){
				IOFunctions::out(LogLevel::ERRO, 'Unable to create new API Key.', true, false, false);
				IOFunctions::out(LogLevel::ERRO, 'Please provide the key data in the format \'<email>,<First_Last>,<whitelist_only_as_integer>\'', true, false, false);
				IOFunctions::out(LogLevel::ERRO, 'Example: php synful.php createkey=jon@acme.com,John_Doe,0', true, false, false);
				exit(2);
			}else{
				$email = $new_key_data[0];
				$name = str_replace('_', ' ', $new_key_data[1]);
				$whitelist_only = intval($new_key_data[2]);

				if(APIKey::keyExists($email)){
					IOFunctions::out(LogLevel::ERRO, 'A key with that email is already defined.', true, false, false);
					exit(2);
				}

				IOFunctions::out(LogLevel::INFO, 'Creating new key with data: ', true, false, false);
				IOFunctions::out(LogLevel::INFO, '    Name: ' . $name, true, false, false);
				IOFunctions::out(LogLevel::INFO, '    Email: ' . $email, true, false, false);

				IOFunctions::out(LogLevel::INFO, '------------------------------------------------', true, false, false);

				if(APIKey::addNew($name, $email, $whitelist_only, 0, true) == NULL){
					IOFunctions::out(LogLevel::ERRO, 'There was an error while creating your new API Key.', true, false, false);
				}

				exit(2);
			}
		}

		/**
		 * Handle createhandler CLI Parameter
		 * @param  String $value The value set in CLI
		 */
		public static function createhandler($value){
			$value = str_replace('_', '', $value);
			$value = trim($value);

			if(!ctype_alpha($value)){
				IOFunctions::out(LogLevel::ERRO, 'Error: Request Handler names must only contain alphabetic characters and no spaces. Title case recommended -> (ThisIsTitleCase).', true);
				exit(0);
			}else{
				if(!file_exists('./src/Synful/RequestHandlers/' . $value . '.php')){
					file_put_contents('./src/Synful/RequestHandlers/' . $value . '.php', "<?php\r\n\r\n    namespace Synful\RequestHandlers;\r\n\r\n    use Synful\Synful;\r\n    use Synful\RequestHandlers\Interfaces\RequestHandler;\r\n    use Synful\Response;\r\n\r\n    class " . $value . " implements RequestHandler {\r\n\r\n    	/**\r\n    	 * Change this to 'true' if you want your request handler to be publically accessible\r\n    	 * Note: When set to 'true', this request handler will not require an API Key\r\n    	 */\r\n    	public function __construct(){\r\n    		". '$this->is_public' . " = false;\r\n    	}\r\n\r\n		/**\r\n		 * Function for handling request and returning data as a Response object\r\n		 * @param  Response " . '$data' . "              The data received by reference\r\n		 * @param  boolean  " . '$is_master_request' . " True if the key being used to access the request is a master key\r\n		 */\r\n		public function handleRequest(Response &" . '$data' . ", " . '$is_master_request' . " = false){\r\n			" . '$request_data =& $data->request;' . "\r\n\r\n			// Insert your code here\r\n\r\n		}\r\n\r\n	}\r\n\r\n?>\r\n");
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