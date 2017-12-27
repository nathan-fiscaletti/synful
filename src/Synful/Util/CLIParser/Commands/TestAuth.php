<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Synful;
use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class TestAuth extends Command
{
    /**
     * Construct the Color command.
     */
    public function __construct()
    {
        $this->name = 'ta';
        $this->description = 'Test authentication for an API key.';
        $this->required = false;
        $this->alias = 'test-auth';

        $this->exec = function ($auth, $key) {
            $api_key = APIKey::getkey($auth);
            if ($api_key == null) {
                sf_error('Error: No API Key found matching that authentication handle.', true);

                exit;
            } 

            if (! $api_key->authenticate($key)) {
                sf_info(
                    'Authentication for APIKey with auth \''.sf_color($auth, 'light_blue').
                    '\' has '.sf_color('failed', 'light_red').'.',
                    true,
                    false,
                    false
                );

                exit;
            }

            sf_info(
                'Authentication for APIKey with auth \''.sf_color($auth, 'light_blue').
                '\' has '.sf_color('succeeded', 'light_green').'.',
                true,
                false,
                false
            );

            exit;
        };
    }
}
