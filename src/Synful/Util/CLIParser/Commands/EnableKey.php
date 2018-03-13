<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class EnableKey extends Command
{
    /**
     * Construct the EnableKey command.
     */
    public function __construct()
    {
        $this->name = 'ek';
        $this->description = 'Enables a key that has been disabled based on authentication handle or ID.';
        $this->required = false;
        $this->alias = 'enable-key';
        $this->exec = function ($auth_or_id) {
            $key = APIKey::getKey($auth_or_id);
            if ($key !== null) {
                $key->enabled = true;
                $key->save();
                sf_info(
                    'APIKey for ID \''.sf_color($auth_or_id, 'light_blue').
                    '\' has been '.sf_color('enabled', 'light_green').'.',
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
