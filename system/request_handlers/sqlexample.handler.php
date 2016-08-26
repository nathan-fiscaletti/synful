<?php

	include_once './system/request_handlers/request_handler.interface.php';

    class SqlExample implements RequestHandler {

        public function handleRequest(Response &$data, $is_master_request = false){

        	// Create a reference to our request object
        	$request =& $data->request;
			
			/* 
				Define SQL Database in 'config.ini' as follows
				
				...
				[sql_databases]
				db_name="['host', 'username', 'password', 'db_name', port]"
				...

			*/
			
			// Create a reference to the SQL Database Connection
			// (This is the actual name of the database, not it's key in 'config.ini')
        	$sql_con =& Synful::$sql_databases['db_name'];

			// Validate the request
			if(!isset($request['id']) || !is_int($request['id'])){
				$data->code = 400;
				$data->setResponse('error', 'Bad Request: Invalid ID supplied');
			}else{

				// Do not use $sql_con->openSql();
				// The connection has already been opened by Synful and will be closed as needed. 

				// Query MySql for the user row
				// Parameters: Query String, Boolean set to true to return a result set, 
				// array containing type definitions and parameter binds
				$result = $sql_con->executeSql('select * from mytable where id = ?', true, ['i', $request['id']]);

				// Convert the data from SQL to an array
				$db_row = mysqli_fetch_assoc($result);

				// Set the response code
				$data->code = 200;

				// Overload the response with the data stored in the database row
				$data->overloadResponse($db_row);

				// Alternately, you can set each response field manually
				$data->setResponse('id', $db_row['id']);
				$data->setResponse('name', $db_row['name']);
				$data->setResponse('foo', 'bar');

				// Do not use $sql_con->closeSql();
				// The connection will be closed as needed by Synful automatically

			}

		}
	}

?>
