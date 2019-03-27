<?php

namespace Synful\CLIParser\Commands;

use Synful\CLIParser\Commands\Util\Command;

class Output extends Command
{
    /**
     * Construct the Output command.
     */
    public function __construct()
    {
        $this->name = 'o';
        $this->description = 'Minimizes output when creating or updating api keys. Must be set first.';
        $this->required = false;
        $this->alias = 'output';
        $this->exec = function ($level) {
            global $argv;
            if (substr($argv[1], 0, 7) == '-output' || substr($argv[1], 0, 2) == '-o') {
                if (! is_numeric($level) || (intval($level) != 0 && intval($level) != 1)) {
                    sf_error('Output level must either be 0 or 1.', true, false, false);

                    return parameter_result_halt();
                }

                global $__minimal_output;
                if ($level == '0') {
                    $__minimal_output = true;
                } else {
                    $__minimal_output = false;
                }
            } else {
                sf_error(
                    'Output argument must be used before any other argument in'.
                    ' order to be active.',
                    true,
                    false,
                    false
                );

                return parameter_result_halt();
            }

            return intval($level);
        };
    }
}
