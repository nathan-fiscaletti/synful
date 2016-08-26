<?php
	include './system/standalone/standalone.class.php';
	include './system/data_management/sqlconnection.class.php';

	class Synful {

		public static $config;
		public static $sql;
		public static $controller;
		public static $sql_databases;
		static $request_handlers = [];

		/**
		 * Initializes Synful API using either Standalone Mode or Local Web Server
		 */
		public function initialize(){

			if(sizeof(Synful::$config['sql_databases']) > 0){
				IOFunctions::out(LogLevel::NOTE, 'Loading databases...');
				$this->loadSqlDatabases(Synful::$config['sql_databases']);
			}else{
				IOFunctions::out(LogLevel::ERRO, 'Error: Missing Synful database definintion');
				IOFunctions::out(LogLevel::ERRO, 'Match the \'main_database\' setting in config.ini to the correct database. Default Synful database is for storing API Keys, Users and Permissions.');
				exit(1);
			}

			Synful::$sql =& Synful::$sql_databases[Synful::$config['system']['main_database']];

			$this->createDefaultTables();

			Synful::$controller = new Controller();

			Synful::$controller->generateMasterKey();

			IOFunctions::out(LogLevel::NOTE, 'Loading Request Handlers...');
			$this->loadRequestHandlers();

			if(Synful::$config['system']['standalone']){
				IOFunctions::out(LogLevel::NOTE, 'Running in standalone mode...');
				Synful::postStartUp();
				(new Standalone(Synful::$config))->initialize();
			}else{
				$this->run();
			}

		}

		/**
		 * Loads all MySQL Databases into Synful
		 */
		private function loadSqlDatabases($databases){
			foreach($databases as $database){
				$database = (Array)json_decode(str_replace('\'', '"', $database));
				if(sizeof($database) < 5){
					IOFunctions::out(LogLevel::ERRO, 'Failed one or more custom databases. Please check config.ini.', true);
					exit(1);
				}else{
					$new_sql_connection = new SqlConnection($database[0], $database[1], $database[2], $database[3], $database[4]);
					if($new_sql_connection->testConnection()){
						Synful::$sql_databases[$database[3]] = $new_sql_connection;
						Synful::$sql_databases[$database[3]]->openSQL();
						IOFunctions::out(LogLevel::NOTE, '    Connected to database: ' . $database[3]);
					}else{
						IOFunctions::out(LogLevel::ERRO, 'Failed on one or more database connections. Please check config.ini.', true);
						exit(1);
					}
				}
			}
		}

		/**
		 * Loads all request handlers stored in 'system/request_handlers' into system
		 */
		private function loadRequestHandlers(){
			$enabled_request_handler = false;
			foreach(scandir('./system/request_handlers') as $handler){
				if($handler != '.' && $handler != '..' && $handler != 'request_handler.interface.php'){
					$enabled_request_handler = true;
					include './system/request_handlers/' . $handler;
					$class_name = explode('.', $handler)[0];
					eval('Synful::$request_handlers[] = new ' . $class_name . '();');
					IOFunctions::out(LogLevel::NOTE, '    Loaded Request Handler: ' . $class_name);
				}
			}
			if(!$enabled_request_handler){
				IOFunctions::out(LogLevel::WARN, 'No request handlers found. Use \'php synful.php createhandler=handlername\' to create a new handler');
			}
		}

		/**
		 * Runs the API thread on the local web server and outputs it's response in JSON format
		 */
		private function run(){

			if(empty($_POST['request'])){
				$response = new Response();
				$response->code = 400;
				$response->setResponse('message', 'Bad Request');
			}else{
				$response = Synful::$controller->handleRequest($_POST['request'], $this->getClientIP());
			}

			header("Content-Type: text/json");
			http_response_code($response->code);
			IOFunctions::out(LogLevel::RESP, json_encode($response), true, true);

		}

		/**
		 * Retreives the client IP Address
		 * @return String The ip of the remote client
		 */
		private function getClientIP(){
			$ipaddress = '';

		    if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
		    else if(getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		    else if(getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
		    else if(getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
		    else if(getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
		    else if(getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
		    else $ipaddress = 'UNKNOWN';

		    return $ipaddress;
		}

		/**
		 * Create default Synful tables
		 * @return  True if tables were successfuly created
		 */
		private function createDefaultTables(){
			return (
				Synful::$sql->executeSql("CREATE TABLE IF NOT EXISTS `api_keys` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `api_key` VARCHAR(255) NOT NULL , `whitelist_only` INT NOT NULL , `is_master` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;")

				&& Synful::$sql->executeSql("CREATE TABLE IF NOT EXISTS `api_perms` ( `api_key_id` INT UNSIGNED NOT NULL , `put_data` INT NOT NULL , `get_data` INT NOT NULL , `mod_data` INT NOT NULL , PRIMARY KEY (`api_key_id`) ) ENGINE = MyISAM;")

				&& Synful::$sql->executeSql("CREATE TABLE IF NOT EXISTS `ip_firewall` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `api_key_id` INT UNSIGNED NOT NULL , `ip` VARCHAR(255) NOT NULL , `block` INT NOT NULL , PRIMARY KEY (`id`) ) ENGINE = MyISAM;")
			);
		}

		/**
		 * Function to be called after startup has been completed
		 */
		public static function postStartUp(){
			IOFunctions::out(LogLevel::NOTE, '---------------------------------------------------', false, false, false);
		}

		/**
		 * Function to be called prior to start up running
		 */
		public static function preStartUp(){
			IOFunctions::out(LogLevel::NOTE, '---------------------------------------------------', false, false, false);
			IOFunctions::out(LogLevel::NOTE, 'Synful API Initializing...');
		}
	}
?>