<?php

namespace Synful\RequestHandlers;

use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Response;
use Synful\Synful;

/**
 * Class used to demonstrate Custom Sql Connections
 */
class SqlExample implements RequestHandler
{

    /**
     * Function for handling request and returning data as a Response object
     *
     * @param  Response $data
     * @param  boolean  $is_master_request
     */
    public function handleRequest(Response &$data, $is_master_request = false)
    {

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
        if (!isset($request['id']) || !is_int($request['id'])) {
            $data->code = 400;
            $data->setResponse('error', 'Bad Request: Invalid ID supplied');
        } else {
            // Do not use $sql_con->openSql();
            // The connection has already been opened by Synful and will be closed as needed.

            // Query MySql for the user row
            // Parameters: Query String, array containing type definitions and parameter binds,
            // Boolean set to true to return a result set
            $result = $sql_con->executeSql('SELECT * FROM `mytable` WHERE `id`=?', ['i', $request['id']], true);

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
