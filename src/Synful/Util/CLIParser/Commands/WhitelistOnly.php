<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

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

        $this->exec = function ($auth_or_id, $value) {
            if (! is_numeric($value)) {
                sf_error('Value must be an integer of either 0 or 1.');
            } else {
                $key = APIKey::getkey($auth_or_id);
                if ($key !== null) {
                    $key = APIKey::getKey($auth_or_id);
                    $key->whitelist_only = $value;
                    $key->save();
                    sf_info(
                        'Key \''.sf_color($auth_or_id, 'light_green').
                        '\' updated with new White-List Only value \''.
                        (($value == '1' || $value == 1)
                            ? sf_color(
                                $value,
                                'light_green'
                            )
                            : sf_color(
                                $value,
                                'light_red'
                            )
                        ).'\'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error(
                        'No key found for authentication handle/ID \''.$auth_or_id.'\'.',
                        true,
                        false,
                        false
                    );
                }
            }

            exit;
        };
    }
}
