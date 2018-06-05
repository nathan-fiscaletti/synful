<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\Data\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class RemoveKey extends Command
{
    /**
     * Construct the RemoveKey command.
     */
    public function __construct()
    {
        $this->name = 'rk';
        $this->description = 'Removes a key from the System based on authentication handle.';
        $this->required = false;
        $this->alias = 'remove-key';
        $this->exec = function ($auth) {
            $key = APIKey::getApiKey($auth);
            if ($key !== null) {
                $key->delete();
                sf_info(
                    'APIKey for Authentication Handle \''.
                    sf_color($auth, 'light_blue').
                    '\' has been '.sf_color('removed', 'light_red').'.',
                    true,
                    false,
                    false
                );
            } else {
                sf_error('No key was found with that ID.', true, false, false);
            }

            return parameter_result_halt();
        };
    }
}
