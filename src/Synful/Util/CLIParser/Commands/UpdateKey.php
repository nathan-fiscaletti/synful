<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

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

        $this->exec = function ($email_or_id) {
            if (APIKey::keyExists($email_or_id)) {
                $key = APIKey::getKey($email_or_id);

                global $__minimal_output;
                if (! $__minimal_output) {
                    $__minimal_output = false;
                }

                $key->regen(true, $__minimal_output);
            } else {
                sf_error('A key with that email does not exist.', true, false, false);
            }

            exit;
        };
    }
}
