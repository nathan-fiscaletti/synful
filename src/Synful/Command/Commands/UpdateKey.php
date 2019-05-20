<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class UpdateKey extends Command
{
    /**
     * Construct the UpdateKey command.
     */
    public function __construct()
    {
        $this->name = 'uk';
        $this->description = 'Generates a new key for an API Key.';
        $this->required = false;
        $this->alias = 'update-key';

        $this->exec = function ($auth) {
            $key = APIKey::getApiKey($auth);
            if ($key !== null) {
                global $__minimal_output;
                if (! $__minimal_output) {
                    $__minimal_output = false;
                }

                $key->regen(true, $__minimal_output);
            } else {
                sf_error(
                    'A key with that authentication handle does not exist.',
                    true,
                    false,
                    false
                );
            }

            return parameter_result_halt();
        };
    }
}
