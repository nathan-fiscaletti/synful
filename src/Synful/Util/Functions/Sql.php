<?php

if (! function_exists('sf_sql')) {

    /**
     * Execute a Sql Query on the primary Synful Database.
     * 
     * @param string     $query
     * @param array      $binds
     * @param bool       $return
     * @return ResultSet
     */
    function sf_sql($query, $binds = [], $return = false)
    {
        return \Synful\Synful::$sql->executeSql($query, $binds, $return);
    }

}