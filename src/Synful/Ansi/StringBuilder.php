<?php

namespace Synful\Ansi;

use Ansi\StringBuilder as Ansi;

class StringBuilder extends Ansi
{
    /**
     * Converts the StringBuilder into a String.
     *
     * @return string The String
     */
    public function __toString()
    {
        if (! sf_conf('system.color')) {
            $this->stripAnsi();
            return parent::__toString();
        }

        return parent::__toString();
    }
}