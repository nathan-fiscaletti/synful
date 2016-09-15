<?php

namespace Synful\DataManagement;

use MySqli;

/**
 * Class used for handling a MySql connection.
 */
final class SqlConnection
{
    /**
     * The prepared statements that are queue'd for execution.
     *
     * @var array
     */
    private $prepared_statements = [];

    /**
     * The MySqli Object.
     *
     * @var MySqli
     */
    private $sql = null;

    /**
     * The insert id from the last query.
     *
     * @var int
     */
    private $insert_id = -1;

    /**
     * The status of the connection.
     *
     * @var bool
     */
    private $is_open = false;

    /**
     * Connection host.
     *
     * @var string
     */
    private $host;

    /**
     * Connection username.
     *
     * @var string
     */
    private $username;

    /**
     * Connection password.
     *
     * @var string
     */
    private $password;

    /**
     * Connection database.
     *
     * @var string
     */
    private $database;

    /**
     * Connection port.
     *
     * @var int
     */
    private $port;

    /**
     * Create a new SqlConnection instance.
     *
     * @param string  $host
     * @param string  $username
     * @param string  $password
     * @param string  $database
     * @param int $port
     */
    public function __construct(
        string $host = null,
        string $username = null,
        string $password = null,
        string $database = null,
        int $port = null
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    /**
     * Close the Sql Connection and destroy the object.
     */
    public function __destruct()
    {
        $this->closeSQL();
    }

    /**
     * Executes a query in sql.
     *
     * @param string  $query
     * @param array   $binds
     * @param bool $return
     *
     * @return ResultSet
     */
    public function executeSql($query, $binds = [], $return = false)
    {
        $id = $this->prepareStatement(count($this->prepared_statements), $query);
        $ret = null;
        if ($id !== null) {
            $ret = $this->executePreparedStatement($id, $binds, $return);
        }

        return $ret;
    }

    /**
     * Closes the Sql connection.
     */
    public function closeSql()
    {
        if ($this->sql != null && $this->is_open) {
            $this->sql->close();
            $this->sql = null;
        }
    }

    /**
     * Open the Sql connection.
     */
    public function openSql()
    {
        $ret = false;

        $this->closeSQL();

        $this->sql = @new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->sql->connect_errno) {
            $this->sql = null;
        } else {
            $this->is_open = true;
            $ret = true;
        }

        return $ret;
    }

    /**
     * Returns the auto generated id used in the last query.
     *
     * @return int
     */
    public function getLastInsertID()
    {
        return $this->insert_id;
    }

    /**
     * Retrieves the MySqli object associated with the connection.
     *
     * @return MySqli
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Escapes a string for sql injection.
     *
     * @param string $str
     */
    public function escapeString($str)
    {
        return $this->sql->real_escape_string(strip_tags($str));
    }

    /**
     * Retrieves a list of table names for this database.
     *
     * @return array
     */
    public function getTables()
    {
        $result = $this->executeSql('SHOW TABLES', [], true);

        return array_column(mysqli_fetch_all($result), 0);
    }

    /**
     * Executes prepared statement.
     *
     * @param string  $prepareTitle
     * @param bool $return
     *
     * @return ResultSet
     */
    private function executePreparedStatement($prepareTitle, $binds = [], $return = false)
    {
        $ret = null;
        $statement = $this->getPreparedStatement($prepareTitle);
        $tmp = [];
        foreach ($binds as $key => $value) {
            $tmp[$key] = &$binds[$key];
        }

        if (count($binds) > 0) {
            call_user_func_array([$statement, 'bind_param'], $tmp);
            if ($this->sql->errno) {
                trigger_error(
                    'Error while applying binds to SQL Prepared Statement: '.
                    $this->sql->error,
                    E_USER_WARNING
                );
            }
        }

        $ret = $statement->execute();

        if ($statement->errno) {
            trigger_error('Error while executing Prepared Statement: '.$statement->error, E_USER_WARNING);
        }

        $this->insert_id = $statement->insert_id;

        if ($return) {
            $ret = $statement->get_result();
            if ($statement->errno) {
                trigger_error(
                    'Error while retreiving result set from MySQL Prepared Statement: '.
                    $statement->error,
                    E_USER_WARNING
                );
            }
        }

        $this->removePreparedStatement($prepareTitle);

        return $ret;
    }

    /**
     * Create new prepared statement in the system.
     *
     * @param string $prepareTitle
     * @param string $prepareBody
     *
     * @return string
     */
    private function prepareStatement($prepareTitle, $prepareBody)
    {
        $ret = null;
        $this->prepared_statements[$prepareTitle] = $this->sql->prepare($prepareBody);

        if ($this->sql->errno) {
            trigger_error('Error while Preparing SQL: '.$this->sql->error, E_USER_WARNING);
        } else {
            $ret = $prepareTitle;
        }

        return $ret;
    }

    /**
     * Removes a Prepared Statement from the system.
     *
     * @param string $prepareTitle
     */
    private function removePreparedStatement($prepareTitle)
    {
        $this->prepared_statements[$prepareTitle]->close();
        unset($this->prepared_statements[$prepareTitle]);
    }

    /**
     * Retrieves a prepared statement from the storage array.
     *
     * @param string $prepareTitle
     *
     * @return PreparedStatement
     */
    private function getPreparedStatement($prepareTitle)
    {
        return $this->prepared_statements[$prepareTitle];
    }
}
