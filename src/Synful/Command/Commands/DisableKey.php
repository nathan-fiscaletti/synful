<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class DisableKey extends Command
{
    /**
     * Construct the DisableKey command.
     */
    public function __construct()
    {
        $this->name = 'dk';
        $this->description = 'Disables a key (making it unable to be used) based on authentication handle.';
        $this->required = false;
        $this->alias = 'disable-key';

        $this->exec = function ($auth) {
            $key = APIKey::getApiKey($auth);
            if ($key !== null) {
                $key->enabled = false;
                $key->save();
                sf_info(
                    'APIKey for ID \''.sf_color($auth, \Ansi\Color16::LIGHT_BLUE).
                    '\' has been '.sf_color('disabled', \Ansi\Color16::LIGHT_RED).'.',
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
