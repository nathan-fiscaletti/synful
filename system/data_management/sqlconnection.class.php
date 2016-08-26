<?php
	class SqlConnection {
		private $prepared_statements = array();
		private $sql = null;
		private $insert_id = -1;
		private $is_open = false;

		private $host;
		private $username;
		private $password;
		private $database;
		private $port;

		public function __construct($host = null, $username = null, $password = null, $database = null, $port = null){
			$this->host      =  $host;
			$this->username  =  $username;
			$this->password  =  $password;
			$this->database  =  $database;
			$this->port      =  $port;
		}
		
		/** Closes the SQL connection and destroys the object */
		public function __destruct(){
			$this->closeSQL();
		}

		/**
		 * Test the connection to the server
		 * @return boolean true if connection successul, otherwise false
		 */
		public function testConnection(){
			$ret = $this->openSQL();
			$this->closeSQL();
			return $ret;
		}
		
		/**
		 * Escapes a string for sql injection
		 * @param String $str
		 */
		public function escapeString($str){
			return $this->sql->real_escape_string(strip_tags($str));
		}
		
		/**
		 * Executes prepared statement
		 * @param String $prepareTitle The prepared statement to execute
		 * @param Boolean $return If set to true, will return a ResultSet
		 * @return Object The value of the execution
		 */
		 
		public function executePreparedStatement($prepareTitle, $return = false, $binds = array()){
			$result = null;
			if(sizeof($binds) > 0){
				$tmp = array();
				foreach($binds as $key => $value) $tmp[$key] = &$binds[$key];
				call_user_func_array(array($this->prepared_statements[$prepareTitle], 'bind_param'), $tmp);
				if($this->sql->errno){
					trigger_error('Error while applying binds to SQL Prepared Statement: ' . $this->sql->error, E_USER_WARNING);
				}
			}
			
			$ret = $this->prepared_statements[$prepareTitle]->execute();
			
			if($this->prepared_statements[$prepareTitle]->errno){
				trigger_error('Error while Preparing SQL: ' . $this->prepared_statements[$prepareTitle]->error, E_USER_WARNING);
			}
			
			$this->insert_id = $this->prepared_statements[$prepareTitle]->insert_id;
			
			if($return){
				$ret = $this->prepared_statements[$prepareTitle]->get_result();
				if($this->prepared_statements[$prepareTitle]->errno){
					trigger_error('Error while retreiving result set from MySQL Prepared Statement: ' . $this->prepared_statements[$prepareTitle]->error, E_USER_WARNING);
				}
			}
			
			$this->removePreparedStatement($prepareTitle);
			
			return $ret;
		}
		
		/**
		 * Gets a Prepared Statement based on it's title
		 * @param String $prepareTitle The key for the statement
		 * @return PreparedStatement The Prepared Statement
		 */
		public function retrievePreparedStatement($prepareTitle){
			return $this->prepared_statements[$prepareTitle];
		}
		
		/**
		 * Create new prepared statement in the system
		 * @param String $prepareTitle The title for the statement 
		 * @param String $prepareBody The prepared statement
		 * @return String|Boolean The prepareTitle passed or false if prepare failed
		 */
		public function prepareStatement($prepareTitle, $prepareBody){
			$this->prepared_statements[$prepareTitle] = $this->sql->prepare($prepareBody);
			if($this->sql->errno){
				trigger_error('Error while Preparing SQL: ' . mysqli_connect_error(), E_USER_WARNING);
				return false;
			}
			return $prepareTitle;
		}
		
		/**
		 * Removes a Prepared Statement from the system
		 * @param String $prepareTitle The key for the statement
		 */
		public function removePreparedStatement($prepareTitle){
			$this->prepared_statements[$prepareTitle]->close();
			unset($this->prepared_statements[$prepareTitle]);
		}
		
		/**
		 * Executes a query in sql
		 * @param String $query
		 * @param Boolean $return If set to true, will return a ResultSet
		 * @return ResultSet|boolean The result of the query or false if query failed
		 */
		public function executeSql($query, $return = false, $binds = array()){
			$id = $this->prepareStatement(sizeof($this->prepared_statements), $query);
			if($id !== false){
				$ret = $this->executePreparedStatement($id, $return, $binds);
				return $ret;
			}
		}
		
		/**
		 * Closes current SQL connection
		 */
		public function closeSQL(){
			if($this->sql != null && $this->is_open) { $this->sql->close(); $this->sql = null; }
		}
		
		/**
		 * Open SQL connection
		 */
		public function openSQL(){
			$this->closeSQL();

			$this->sql = @new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
			if($this->sql->connect_errno){
				$this->sql = null;
				return false;
			}
			$this->is_open = true;
			return true;
		}
		
		/**
		 * Returns the auto generated id used in the last query
		 * @return The last ID inserted using auto increment column
		 */
		public function getLastInsertID(){
			return $this->insert_id;
		}
		
		/**
		 * Gets the current SQLI Object
		 * @return mysqli The Current SQL Object
		 */
		public function getSQL(){
			return $this->sql;
		}
		
		/**
		 *	Gets the list of prepared statements stored in this class
		 * @return Array(PreparedStatement)
		 */
		public function getPreparedStatements(){
			return $this->prepared_statements;
		}
	}
?>