<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;

class HideConfigChanges extends Command
{
    /**
     * Construct the Color command.
     */
    public function __construct()
    {
        $this->name = 'hc';
        $this->description = 'Used to hide config change messages on initialization.';
        $this->required = false;
        $this->alias = 'hide-config';

        $this->exec = function ($bool) {
            return $bool;
        };
    }
}
