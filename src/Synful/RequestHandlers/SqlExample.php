<?php

namespace Synful\RequestHandlers;

use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Util\Framework\Response;
use Synful\Synful;

/**
 * Class used to demonstrate Custom Sql Connections.
 */
class SqlExample implements RequestHandler
{
    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     * @param  bool  $is_master_request
     */
    public function handleRequest(Response &$response, $is_master_request = false)
    {

        // Create a reference to our request object
        $request = &$response->request;

        // Define SQL Databases and Servers in 'SqlServers.php'
        // Create a reference to the SQL Database Connection
        $sql_con = &Synful::$sql_databases['server_name.db_name'];

        // Validate the request
        if (! isset($request['id']) || ! is_int($request['id'])) {
            $response->code = 400;
            $response->setResponse('error', 'Bad Request: Invalid ID supplied');
        } else {
            // Do not use $sql_con->openSql();
            // The connection has already been opened by Synful and will be closed as needed.

            // Query MySql for the user row
            // Parameters: Query string, array containing type definitions and parameter binds,
            // bool set to true to return a result set
            $result = $sql_con->executeSql('SELECT * FROM `mytable` WHERE `id`=?', ['i', $request['id']], true);

            // Convert the data from SQL to an array
            $db_row = mysqli_fetch_assoc($result);

            // Set the response code
            $response->code = 200;

            // Overload the response with the data stored in the database row
            $response->overloadResponse($db_row);

            // Alternately, you can set each response field manually
            $response->setResponse('id', $db_row['id']);
            $response->setResponse('name', $db_row['name']);
            $response->setResponse('foo', 'bar');

            // Do not use $sql_con->closeSql();
            // The connection will be closed as needed by Synful automatically
        }
    }
}
