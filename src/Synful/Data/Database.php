<?php

namespace Synful\Data;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    /**
     * Used to store the Capsule Manager.
     *
     * @var \Illuminate\Database\Capsule\Manager
     */
    private static $capsule;

    /**
     * Initialize the database connections in the framework.
     *
     * @param array $databases
     */
    public static function initialize($databases)
    {
        self::$capsule = new Capsule();
        foreach ($databases as $connection => $configuration) {
            self::$capsule->addConnection($configuration, $connection);
        }
        self::$capsule->setEventDispatcher(new Dispatcher(new Container));
        self::$capsule->bootEloquent();
    }

    /**
     * Retrieve the currently active Capsule Manager.
     *
     * @return \Illuminate\Database\Capsule\Manager
     */
    public static function capsule()
    {
        return self::$capsule;
    }

    /**
     * Catch all static function calls and if one matches
     * a connection name, return that connection.
     *
     * @return \Illuminate\Database\Connection|null
     */
    public static function __callstatic($connection, $arguments)
    {
        $connectionObj = self::$capsule->getConnection($connection);

        if ($connectionObj == null) {
            throw new \Exception('Call to undefined static function '.$connection);
            return;
        }

        return $connectionObj;
    }
}