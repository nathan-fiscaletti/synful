<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class DisableKey extends Command
{
    /**
     * Construct the DisableKey command.
     */
    public function __construct()
    {
        $this->name = 'dk';
        $this->description = 'Disables a key (making it unable to be used) based on authentication handle or ID.';
        $this->required = false;
        $this->alias = 'disable-key';

        $this->exec = function ($auth_or_id) {
            $key = APIKey::getKey($auth_or_id);
            if ($key !== null) {
                $key->enabled = false;
                $key->save();
                sf_info(
                    'APIKey for ID \''.sf_color($auth_or_id, 'light_blue').
                    '\' has been '.sf_color('disabled', 'light_red').'.',
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
