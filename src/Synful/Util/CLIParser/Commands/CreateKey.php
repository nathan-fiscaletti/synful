<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\Data\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class CreateKey extends Command
{
    /**
     * Construct the CreateKey command.
     */
    public function __construct()
    {
        $this->name = 'ck';
        $this->description = 'Creates a new API key with the specified information.';
        $this->required = false;
        $this->alias = 'create-key';

        $this->exec = function ($auth, $name, $security_level, $whitelist_only, $rate_limit, $rate_limit_seconds) {
            if (! is_numeric($whitelist_only)) {
                sf_error('Unable to create new API Key.', true, false, false);
                sf_error(
                    'Whitelist Only value must be an integer value of either 1 or 0.',
                    true,
                    false,
                    false
                );
            } else {
                if (! is_numeric($security_level) || $security_level < 1) {
                    sf_error('Unable to create new API Key.', true, false, false);
                    sf_error(
                        'Security Level value must be an integer value greater than 0.',
                        true,
                        false,
                        false
                    );
                } else {
                    $key = APIKey::getApiKey($auth);
                    if ($key !== null) {
                        $response = null;
                        while (
                            $response != 'yes' && $response != 'no' &&
                            $response != 'y' && $response != 'n'
                        ) {
                            sf_error('A key with that authentication handle is already defined.', true, false, false);
                            $response = sf_input(
                                'Would you like to update it with this information? (yes/no)',
                                \Synful\Util\IO\LogLevel::INFO
                            );
                            continue;
                        }

                        if ($response == 'yes' || $response == 'y') {
                            $key->name = $name;
                            $key->security_level = $security_level;
                            $key->whitelist_only = $whitelist_only;
                            $key->rate_limit = $rate_limit;
                            $key->rate_limit_seconds = $rate_limit_seconds;
                            $key->save();
                            sf_info('API Key Updated', true, false, false);
                            sf_info(
                                'Belongs To: '.sf_color($key->name, 'light_blue'),
                                true,
                                false,
                                false
                            );
                            sf_info(
                                '    Auth Handle    : '.$key->auth.' / '.$key->id,
                                true,
                                false,
                                false
                            );
                            sf_info(
                                '    Whitelist-Only : '.
                                (($key->whitelist_only)
                                    ? sf_color(
                                        'true',
                                        'light_green'
                                    )
                                    : sf_color(
                                        'false',
                                        'light_red'
                                    )
                                ),
                                true,
                                false,
                                false
                            );
                            sf_info(
                                '    Security       : '.sf_color('Level '.$key->security_level, 'light_green'),
                                true,
                                false,
                                false
                            );
                            sf_info(
                                '    Enabled        : '.
                                (($key->enabled)
                                    ? sf_color(
                                        'true',
                                        'light_green'
                                    )
                                    : sf_color(
                                        'false',
                                        'light_red'
                                    )
                                ),
                                true,
                                false,
                                false
                            );
                        } elseif ($response == 'no' || $response == 'n') {
                            return parameter_result_halt();
                        }
                    } else {
                        global $__minimal_output;
                        if (! $__minimal_output) {
                            $__minimal_output = false;
                        }

                        if (! $__minimal_output) {
                            sf_info('Creating new key with data: ', true, false, false);
                            sf_info('    Name: '.$name, true, false, false);
                            sf_info('    Auth Handle: '.$auth, true, false, false);
                            sf_info('------------------------------------------------', true, false, false);
                        }

                        if (
                            APIKey::addNew(
                                $name,
                                $auth,
                                $whitelist_only,
                                $security_level,
                                $rate_limit,
                                $rate_limit_seconds,
                                true,
                                $__minimal_output
                            ) == null
                        ) {
                            sf_error(
                                'There was an error while creating your new API Key.',
                                true,
                                false,
                                false
                            );
                        }
                    }
                }
            }

            return parameter_result_halt();
        };
    }
}
