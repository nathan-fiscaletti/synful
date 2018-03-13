<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class RemoveKey extends Command
{
    /**
     * Construct the RemoveKey command.
     */
    public function __construct()
    {
        $this->name = 'rk';
        $this->description = 'Removes a key from the System based on authentication handle or ID.';
        $this->required = false;
        $this->alias = 'remove-key';
        $this->exec = function ($auth_or_id) {
            $key = APIKey::getKey($auth_or_id);
            if ($key !== null) {
                $key->delete();
                sf_info(
                    'APIKey for ID \''.sf_color($auth_or_id, 'light_blue').
                    '\' has been '.sf_color('removed', 'light_red').'.',
                    true,
                    false,
                    false
                );
            } else {
                sf_error('No key was found with that ID.', true, false, false);
            }

            exit;
        };
    }
}
