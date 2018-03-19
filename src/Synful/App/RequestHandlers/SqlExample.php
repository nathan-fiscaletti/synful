<?php

namespace Synful\App\RequestHandlers;

use Synful\Synful;
use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\MiddleWare\APIKeyValidation;

/**
 * Class used to demonstrate Custom Sql Connections.
 */
class SqlExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/sql/{id}';

    /**
     * Implement the APIKeyValidation middleware
     * in order to require an API key to access
     * this RequestHandler.
     *
     * @var array
     */
    public $middleware = [
        APIKeyValidation::class,
    ];

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        // Define SQL Databases and Servers in 'SqlServers.php'
        // Create a reference to the SQL Database Connection
        $sql_con = sf_db('server_name.db_name');

        // Validate the request
        if ($request->field('id') == null || ! is_int($request->field('id'))) {
            return sf_response(
                400,
                [
                    'error' => 'Bad Request: Invalid ID supplied',
                ]
            );
        } else {
            // Do not use $sql_con->openSql();
            // The connection has already been opened by Synful
            // and will be closed as needed.

            // Query MySql for the user row
            // Parameters: Query string, array containing type
            // definitions and parameter binds, bool set to true
            // to return a result set
            $result = $sql_con->executeSql(
                'SELECT * FROM `mytable` WHERE `id`=?',
                [
                    'i',
                    $request->field('id'),
                ],
                true
            );

            // Convert the data from SQL to an array
            $db_row = mysqli_fetch_assoc($result);

            // Return the row in the response.
            return $db_row;

            // Do not use $sql_con->closeSql();
            // The connection will be closed as
            // needed by Synful automatically.
        }
    }
}
