<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\Data\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

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
                    sf_color($auth, 'light_blue').
                    '\' has been '.sf_color('enabled', 'light_green').'.',
                    true,
                    false,
                    false
                );
            } else {
                sf_error('No key was found with that Authentication Handle.', true, false, false);
            }

            exit;
        };
    }
}
