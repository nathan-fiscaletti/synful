<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

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
                    sf_color($auth, \Ansi\Color::FG_LIGHT_BLUE).
                    '\' has been '.sf_color('removed', \Ansi\Color::FG_LIGHT_RED).'.',
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
