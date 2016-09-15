<?php

namespace Synful\DataManagement;

use Synful\Util\Object;
use Synful;

/**
 * Class used to construct Database Models.
 */
class Model
{

    /**
     * The parent database of the table that this model is for.
     *
     * @var string
     */
    public $database = 'synful';

    /**
     * The identifier to use to locate this item.
     *
     * @var [type]
     */
    private $identifier;

    /**
     * The data pulled from sql
     *
     * @var array
     */
    private $rowData = [];

    private $rows = [];

    /**
     * The id of this row in association with it's identifier.
     *
     * @var mixed
     */
    private $id;

    private $hasMany;

    private $hasOne;

    private $belongsTo;

    /**
     * Create a new instance of a database table model.
     *
     * @param int     $id
     * @param string  $custom_identifier
     */
    public function __construct(
        $id,
        $database = 'synful',
        $custom_identifier = null,
        $has_many = [],
        $has_one = [],
        $belongs_to = []
    ) {
        foreach ($has_many as $model) {
            if (! ($model instanceof self)) {
                trigger_error('Not an instance of \\Synful\\DataManagent\\Model.', E_USER_WARNING);
            }
        }

        $this->id = $id;

        $this->identifier = ($custom_identifier == null)
                          ? $this->getPrimaryKey()
                          : $custom_identifier;

        $this->database = $database;
        $this->hasMany = $has_many;
        $this->hasOne = $has_one;
        $this->belongsTo = $belongs_to;

        $this->load();
    }

    /**
     * Saves the data to the table.
     */
    public function save()
    {
        $sql_connection = Synful::$sql_databases[$this->database];
        $keys = $values = $update = '';
        $insert_bind_values = $update_bind_values = [];
        $insert_bind_types = '';

        foreach ($this->rowData as $key => $value) {
            $keys .= ($keys == '') ? $key : ', '.$key;
            $values .= ($values == '') ? '?' : ', ?';
            $update .= ($update == '') ? $key.'=?' : ', '.$key.'=?';
            $insert_bind_values[] = $value;
            $update_bind_values[] = $value;
            $insert_bind_types .= ($this->identifier != null) ? 'ss' : 's';
        }

        $bind_values = array_merge($insert_bind_values, $update_bind_values);
        $bind_values = array_merge([$insert_bind_types], $bind_values);

        if ($this->identifier != null) {
            $query = 'INSERT INTO '.$this->getTableName().' ('.$keys.
                     ') VALUES ('.$values.') ON DUPLICATE KEY UPDATE '.
                     $update;
        } else {
            if ($this->exists()) {
                $query = 'UPDATE '.$this->getTableName().' SET '.
                         $update.' WHERE '.$this->identifier.' = '.
                         $this->id;
            } else {
                $query = 'INSERT INTO '.$this->getTableName().' ('.$keys.
                         ') VALUES ('.$values.')';
            }
        }

        $sql_connection->executeSql($query, $bind_values);
    }

    /**
     * Check if this row exists.
     *
     * @return bool
     */
    public function exists()
    {
        return Synful::$sql_databases[$this->database]->executeSql(
            'SELECT '.$this->identifier.' FROM '.
            $this->getTableName().' WHERE '.$this->identifier.
            ' = '.$this->id,
            [],
            true
        )->num_rows > 0;
    }

    /**
     * Validates the models integrity.
     *
     * @return bool
     */
    public function validateModel()
    {
        $ret = false;

        if (! array_key_exists($this->database, Synful::$sql_databases)) {
            trigger_error(
                'Bad database definition for \''.$this->getTableName().'\' model.',
                E_USER_WARNING
            );
        } else {
            $ret = $this->tableExists();
        }

        return $ret;
    }

    /**
     * Returns parent SqlConnection object.
     *
     * @return SqlConnection
     */
    public function getParentDatabase()
    {
        $ret = null;

        if (array_key_exists($this->database, Synful::$sql_databases)) {
            $ret = Synful::$sql_databases[$this->database];
        }

        return $ret;
    }

    /**
     * Handle undefined property calls as row access.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->rowData)) {
            $this->rowData[$key] = $value;
        } else {
            trigger_error('Unknown variable in database model.', E_USER_WARNING);
        }
    }

    /**
     * Handle undefined property calls as row access.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        $ret = null;

        if (array_key_exists($key, $this->rowData)) {
            $ret = $this->rowData[$key];
        }

        return $ret;
    }

    /**
     * Handle undefined function calls as property access.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $ret = $this;
        if (array_key_exists($name, $this->rowData)) {
            if (count($arguments) < 1) {
                $ret = $this->rowData[$name];
            } else {
                $this->rowData[$name] = $arguments[0];
            }
        } else {
            trigger_error('Call to undefined function \''.$name.'\'.', E_USER_WARNING);
        }

        return $ret;
    }

    /**
     * Load the object data into the model.
     */
    private function load()
    {
        if (! $this->validateModel()) {
            trigger_error(
                'No table found for \''.$this->getTableName().'\' model.',
                E_USER_WARNING
            );
        } else {
            $sql_connection = Synful::$sql_databases[$this->database];
            $res = $sql_connection->executeSql(
                'SELECT * FROM '.$this->getTableName().
                ' WHERE '.$this->identifier.
                ' = '.$this->id,
                [],
                true
            );
            $this->rowData = $res->fetch_assoc();
        }
    }

    /**
     * Get the primary key for this table.
     *
     * @return string [description]
     */
    private function getPrimaryKey()
    {
        return mysqli_fetch_assoc(
            Synful::$sql_databases[$this->database]->executeSql(
                'SHOW KEYS FROM '.$this->getTableName().' WHERE Key_name = \'PRIMARY\'',
                [],
                true
            )
        )['Column_name'];
    }

    /**
     * Checks if the table exists in the provided database.
     *
     * @return bool
     */
    private function tableExists()
    {
        return in_array($this->getTableName(), Synful::$sql_databases[$this->database]->getTables());
    }

    /**
     * Returns the name of the table associated with this row.
     *
     * @return string
     */
    private function getTableName()
    {
        return strtolower(get_class($this));
    }
}
