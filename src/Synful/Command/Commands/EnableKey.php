<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class EnableKey extends Command
{
    /**
     * Construct the EnableKey command.
     */
    public function __construct()
    {
        $this->name = 'ek';
        $this->description = 'Enables a key that has been disabled based on Authentication Handle.';
        $this->required = false;
        $this->alias = 'enable-key';
        $this->exec = function ($auth) {
            $key = APIKey::getApiKey($auth);
            if ($key !== null) {
                $key->enabled = true;
                $key->save();
                sf_info(
                    'APIKey for Authentication Handle \''.
                    'APIKey for ID \''.sf_color($auth, \Ansi\Color16::LIGHT_BLUE).
                    '\' has been '.sf_color('enabled', \Ansi\Color16::LIGHT_GREEN).'.',
                    true,
                    false,
                    false
                );
            } else {
                sf_error('No key was found with that Authentication Handle.', true, false, false);
            }

            return parameter_result_halt();
        };
    }
}
