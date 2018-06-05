<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\Data\Models\APIKey;
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
            $api_key = APIKey::getApikey($auth);
            if ($api_key == null) {
                sf_error('Error: No API Key found matching that authentication handle.', true);

                return parameter_result_halt();
            }

            if ($api_key->authenticate($key, 0) !== 1) {
                sf_info(
                    'Authentication for APIKey with auth \''.sf_color($auth, 'light_blue').
                    '\' has '.sf_color('failed', 'light_red').'.',
                    true,
                    false,
                    false
                );

                return parameter_result_halt();
            }

            sf_info(
                'Authentication for APIKey with auth \''.sf_color($auth, 'light_blue').
                '\' has '.sf_color('succeeded', 'light_green').'.',
                true,
                false,
                false
            );

            sf_info(
                'API Key Embedded Security: '.
                sf_color('Level '.$api_key->security_level, 'light_blue'),
                true,
                false,
                false
            );

            return parameter_result_halt();
        };
    }
}
