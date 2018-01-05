<?php

namespace Synful\RequestHandlers;

use Synful\Synful;
use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\MiddleWare\APIKeyValidation;

/**
 * Class used to demonstrate Custom Sql Connections.
 */
class SqlExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/sql';

    /**
     * Implement whatever middleware you would like.
     *
     * @var array
     */
    public $middleware = [
        APIKeyValidation::class,
    ];

    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {

        // Define SQL Databases and Servers in 'SqlServers.php'
        // Create a reference to the SQL Database Connection
        $sql_con = sf_db('server_name.db_name');

        // Validate the request
        if (! isset($request['id']) || ! is_int($request['id'])) {
            return sf_response(
                400,
                [
                    'error' => 'Bad Request: Invalid ID supplied',
                ]
            );
        } else {
            // Do not use $sql_con->openSql();
            // The connection has already been opened by Synful and will be closed as needed.

            // Query MySql for the user row
            // Parameters: Query string, array containing type definitions and parameter binds,
            // bool set to true to return a result set
            $result = $sql_con->executeSql('SELECT * FROM `mytable` WHERE `id`=?', ['i', $request['id']], true);

            // Convert the data from SQL to an array
            $db_row = mysqli_fetch_assoc($result);

            // Return the row in the response.
            return $db_row;

            // Do not use $sql_con->closeSql();
            // The connection will be closed as needed by Synful automatically
        }
    }
}
