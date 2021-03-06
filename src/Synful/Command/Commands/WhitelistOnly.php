<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class WhitelistOnly extends Command
{
    /**
     * Construct the WhitelistOnly command.
     */
    public function __construct()
    {
        $this->name = 'w';
        $this->description = 'Enables or disables the \'White-List Only\' Option for the specified key.';
        $this->required = false;
        $this->alias = 'white-list-only';

        $this->exec = function ($auth, $value) {
            if (! is_numeric($value)) {
                sf_error('Value must be an integer of either 0 or 1.');
            } else {
                $key = APIKey::getApikey($auth);
                if ($key !== null) {
                    $key = APIKey::getApiKey($auth);
                    $key->whitelist_only = $value;
                    $key->save();
                    sf_info(
                        'Key \''.sf_color($auth, \Ansi\Color::FG_LIGHT_GREEN).
                        '\' updated with new White-List Only value \''.
                        (($value == '1' || $value == 1)
                            ? sf_color(
                                $value,
                                \Ansi\Color::FG_LIGHT_GREEN
                            )
                            : sf_color(
                                $value,
                                \Ansi\Color::FG_LIGHT_RED
                            )
                        ).'\'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error(
                        'No key found for authentication handle \''.$auth.'\'.',
                        true,
                        false,
                        false
                    );
                }
            }

            return parameter_result_halt();
        };
    }
}
