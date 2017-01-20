<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

class DisableKey extends Command
{
    /**
     * Construct the DisableKey command.
     */
    public function __construct()
    {
        $this->name = 'dk';
        $this->description = 'Disables a key (making it unable to be used) based on email or ID.';
        $this->required = false;
        $this->alias = 'disable-key';

        $this->exec = function ($email_or_id) {
            if (APIKey::keyExists($email_or_id)) {
                $key = APIKey::getKey($email_or_id);
                $key->enabled = false;
                $key->save();
                sf_info(
                    'APIKey for ID \''.sf_color($email_or_id, 'light_blue').
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