<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Synful;
use Synful\Util\CLIParser\Commands\Util\Command;

class CreateTables extends Command
{
    /**
     * Construct the CreateTables command.
     */
    public function __construct()
    {
        $this->name = 'ct';
        $this->description = 'Create the default MySql tables for Synful.';
        $this->required = false;
        $this->alias = 'create-tables';

        $this->exec = function () {
            Synful::createDefaultTables();
            sf_info(
                'Created default tables.',
                true,
                false,
                false
            );

            exit;
        };
    }
}
