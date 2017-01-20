<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

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

        $this->exec = function ($email, $name, $whitelist_only) {
            if (! is_numeric($whitelist_only)) {
                sf_error('Unable to create new API Key.', true, false, false);
                sf_error(
                    'Whitelist Only value must be an integer value of either 1 or 0.',
                    true,
                    false,
                    false
                );
            } else {
                if (APIKey::keyExists($email)) {
                    sf_error('A key with that email is already defined.', true, false, false);
                } else {
                    global $__minimal_output;

                    if (! $__minimal_output) {
                        sf_info('Creating new key with data: ', true, false, false);
                        sf_info('    Name: '.$name, true, false, false);
                        sf_info('    Email: '.$email, true, false, false);
                        sf_info('------------------------------------------------', true, false, false);
                    }

                    if (APIKey::addNew($name, $email, $whitelist_only, 0, true, $__minimal_output) == null) {
                        sf_error(
                            'There was an error while creating your new API Key.',
                            true,
                            false,
                            false
                        );
                    }
                }
            }

            exit;
        };
    }
}