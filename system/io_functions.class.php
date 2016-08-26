<?php

	include './system/colors.class.php';

	class IOFunctions {

		/**
		 * Loads configuration file into system
		 * @return boolean true if config is loaded, false otherwise
		 */
		public static function loadConfig(){
			if(file_exists('./config.ini')){
				try{
					Synful::$config = parse_ini_file('./config.ini', true);
					return true;
				}catch(Exception $ex){
					trigger_error('Failed to load config: ' . $ex->message, E_USER_ERROR);
					return false;
				}
			}
		}

		/**
		 * Prints output to the console and logs
		 * @param  integer $level                The debug level of the message
		 * @param  string  $data                 The data to output
		 * @param  boolean $force                If set to true, the message will be forcably sent to console
		 * @param  boolean $block_header_on_echo If set to true, when the message is output with an echo the header information will be ommited
		 * @param  boolean $write_to_file        If set to false, text will not be written to the log file
		 */
		public static function out($level, $data, $force = false, $block_header_on_echo = false, $write_to_file = true){
			$head = ($level == 0) ? 'INFO' : (($level == 1) ? 'WARN' : (($level == 2) ? 'NOTE' : (($level == 3) ? 'ERRO' : (($level == 4) ? 'RESP' : 'INFO' ))));

			$log_file = Synful::$config['files']['logfile'];

			if(!file_exists(dirname($log_file))){
				mkdir(dirname($log_file), 0700, true);
				chown(dirname($log_file), exec('whoami'));
				chmod(dirname($log_file), 0700);
			}


			foreach(preg_split('/\n|\r\n?/', $data) as $line){
				if(Synful::$config['system']['standalone'] || $force) 
					echo ($block_header_on_echo) ? $line : '[' . Colors::cs('SYNFUL', 'white') . '] ' . IOFunctions::parseLogString($level, $head, $line) . "\r\n";

				if(Synful::$config['files']['log_to_file'] && $write_to_file){
					if(Synful::$config['files']['split_logfiles']){
						$log_id = 0;
						while(file_exists($log_file) && (count(file($log_file)) - 1) > Synful::$config['files']['max_logfile_lines']){
							$log_id++;
							$log_file = Synful::$config['files']['logfile'] . '.' . $log_id;
						}
					}

					if(!file_exists($log_file)){
						@file_put_contents($log_file, '');
						chmod($log_file, 0700);
						chown($log_file, exec('whoami'));
					}

					if(is_writable($log_file)){
						file_put_contents($log_file, '[' . time() . '] [SYNFUL] [' . $head . '] ' . $line . "\r\n", FILE_APPEND);
					}else{
						echo '[' . Colors::cs('SYNFUL', 'white') . '] ' . IOFunctions::parseLogString(LogLevel::ERRO, 'ERRO', 'Failed to write to config file. Check permissions?') . "\r\n";
						echo '[' . Colors::cs('SYNFUL', 'white') . '] ' . IOFunctions::parseLogString(LogLevel::ERRO, 'ERRO', 'Disabling logging for the rest of the session'). "\r\n";
						Synful::$config['files']['log_to_file'] = false;
					}
				}

			}
		}

		/**
		 * Used to catch error output from PHP and forward it to our log file
		 */
		public static function catch_error($errno, $errstr, $errfile, $errline){
			
			$err = $errstr . ' in ' . $errfile . ' at line ' . $errline;

			switch ($errno) {
    			case E_USER_ERROR : {
    				IOFunctions::out(LogLevel::ERRO, 'Fatal Error: ' . $err);
    				break;		
    			}

    			case E_USER_WARNING : {
					IOFunctions::out(LogLevel::WARN, 'Warning: ' . $err);
					break;
    			}

    			case E_USER_NOTICE : {
					IOFunctions::out(LogLevel::NOTE, 'Notice: ' . $err);
					break;
    			}

    			default : {
    				IOFunctions::out(LogLevel::ERRO, 'Unknown Error: ' . $err);
    				break;
    			}
    		}

    		return true;
			
		}

		/**
		 * Handles system shut down, closes out SQL Connection
		 */
		public static function on_shut_down(){
			if(Synful::$sql != null) Synful::$sql->closeSQL();

			if(sizeof(Synful::$sql_databases) > 0){
				foreach(Synful::$sql_databases as $database){
					$database->closeSQL();
				}
			}

			IOFunctions::out(LogLevel::INFO, 'Synful API Shutdown!');
		}

		/**
		 * Parses a log string with color codes and any other nessecary parsing 
		 * 
		 * @param  LogLevel $level   The log level of the message
		 * @param  String   $head    The header tag for the message
		 * @param  String   $message The message to parse
		 * @return String            The fully parsed message
		 */
		private static function parseLogString($level, $head, $message){
			$return_string = "";

			if(Synful::$config['system']['color']){
				switch($level){
					case LogLevel::INFO : {
						$return_string = '[' . Colors::cs($head, 'light_green') . '] ' . Colors::cs($message, 'white');
						break;
					}

					case LogLevel::WARN : {
						$return_string = '[' . Colors::cs($head, 'light_red') . '] ' . Colors::cs($message, 'yellow');
						break;
					}

					case LogLevel::NOTE : {
						$return_string = '[' . Colors::cs($head, 'light_blue') . '] ' . Colors::cs($message, 'white');
						break;
					}

					case LogLevel::ERRO : {
						$return_string = '[' . Colors::cs($head, 'light_red') . '] ' . Colors::cs($message, 'red');
						break;
					}

					case LogLevel::RESP : {
						$return_string = '[' . Colors::cs($head, 'light_cyan') . '] ' . Colors::cs($message, 'cyan');
						break;
					}

					default : {
						$return_string = '[' . Colors::cs($head, 'light_green') . '] ' . Colors::cs($message, 'white');
					}
				}
			}else{
				$return_string = '[' . $head . '] ' . $message;
			}

			return $return_string;
		}

	}



	/**
	 * Class used to store log level constants
	 */
	class LogLevel {
		const INFO = 0;
		const WARN = 1;
		const NOTE = 2;
		const ERRO = 3;
		const RESP = 4;
	}

	
?>
