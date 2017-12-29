<?php

if (! function_exists('sf_sql')) {

    /**
     * Execute a Sql Query on the primary Synful Database.
     *
     * @param string     $query
     * @param array      $binds
     * @param bool       $return
     * @param string     $database
     * @return ResultSet
     */
    function sf_sql($query, $binds = [], $return = false, $database = 'main.synful')
    {
        if (\Synful\Synful::$sql_databases[$database] == null) {
            if ($database == 'main.synful') {
                trigger_error(
                    'Primary database not configured. '
                );
            } else {
                trigger_error(
                    'Missing Synful database definition. '.
                    'Set \'sqlservers.main.databases.synful\' in \'SqlServers.php\'. '.
                    'Default Synful database is used for storing API Keys.',
                    E_USER_WARNING
                );
                exit();
            }
            exit;
        }

        try {
            return \Synful\Synful::$sql_databases[$database]->executeSql(
                $query,
                $binds,
                $return
            );
        } catch (\Synful\Util\Framework\SynfulException $e) {
            if ($database == 'main.synful') {
                trigger_error(
                    'Failed to connect to the primary Synful database. '.
                    'Please check SqlServers.php. '.
                    'Default Synful database is used for storing API Keys.',
                    E_USER_WARNING
                );
                exit;
            } else {
                trigger_error(
                    'Failed to connect to custom database \''.$database.'\'. Please check SqlServers.php.',
                    E_USER_WARNING
                );
                exit;
            }
        }
    }
}
