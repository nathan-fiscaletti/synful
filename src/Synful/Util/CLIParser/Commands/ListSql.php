<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

class ListSql extends Command
{
	/**
	 * Construct the ListSql command.
	 */
	public function __construct()
	{
		$this->name = 'lsql';
		$this->description = 'Lists the Sql Servers and child databases stored in SqlServers.php';
		$this->required = false;
		$this->alias = 'list-sql';
		$this->exec = function () {
			sf_info(
	            'Sql Server List',
	            true,
	            false,
	            false
	        );
	        sf_info(
	            '---------------------------------------------------',
	            true,
	            false,
	            false
	        );
	        foreach (sf_conf('sqlservers') as $server_name => $server) {
	            sf_info(
	                ' | '.sf_color($server_name, 'light_green'),
	                true,
	                false,
	                false
	            );
	            sf_info(
	                ' --------------------------------------------------',
	                true,
	                false,
	                false
	            );
	            sf_info(
	                ' | Server Info  : ['.$server['host'].', '.$server['port'].']',
	                true,
	                false,
	                false
	            );
	            $dbs = ' | Databases    : ';
	            $database_info = '';
	            foreach ($server['databases'] as $database_name => $database) {
	                $database_info .= ($database_info == '') ? '['.$database_name : ', '.$database_name;
	            }
	            sf_info(
	                $dbs.$database_info.']',
	                true,
	                false,
	                false
	            );
	            sf_info(
	                '---------------------------------------------------',
	                true,
	                false,
	                false
	            );
	        }

	        exit;
		};
	}
}