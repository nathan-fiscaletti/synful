<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class EndPointManage extends Command
{
    /**
     * TODO
     * 
     * Command for handing adding and removing endpoints to an API Key.
     */
    public function __construct()
    {
        $this->name = 'epm';
        $this->description = 'Manages the End Points accessible by an API key.';
        $this->required = false;
        $this->alias = 'end-point-manage';

        $this->exec = function ($action, $auth, $endpoint) {
            if (! ($action == 'add' || $action == 'remove')) {
                sf_error(
                    'Invalid action. Valid actions are `add` and `remove`.',
                    true,
                    false,
                    false
                );

                return parameter_result_halt();
            }

            $key = APIKey::getApikey($auth);
            if ($key == null) {
                sf_error(
                    'No key found for authentication handle \''.$auth.'\'.',
                    true,
                    false,
                    false
                );

                return parameter_result_halt();
            }

            if ($action == 'add') {
                if ($key->addRequestHandler($endpoint) && $key->save()) {
                    sf_info(
                        'Endpoint '.sf_color($endpoint, \Ansi\Color::FG_LIGHT_BLUE).
                        ' '.sf_color('added', \Ansi\Color::FG_LIGHT_GREEN).
                        ' to API key '.sf_color($auth, \Ansi\Color::FG_LIGHT_BLUE).'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error(
                        'An error occured while adding the endpoint to the key.',
                        true,
                        false,
                        false
                    );
                    sf_error(
                        'Either the endpoint does not exist, or there was a '.
                        'problem communicating with the database.',
                        true,
                        false,
                        false
                    );
                }

                return parameter_result_halt();
            } elseif ($action == 'remove') {
                if ($key->removeRequestHandler($endpoint) && $key->save()) {
                    sf_info(
                        'Endpoint '.sf_color($endpoint, \Ansi\Color::FG_LIGHT_BLUE).
                        ' '.sf_color('removed', \Ansi\Color::FG_LIGHT_RED).
                        ' from API key '.sf_color($auth, \Ansi\Color::FG_LIGHT_BLUE).'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error(
                        'An error occured while adding the endpoint to the key.',
                        true,
                        false,
                        false
                    );
                    sf_error(
                        'Either the endpoint does not exist, or there was a '.
                        'problem communicating with the database.',
                        true,
                        false,
                        false
                    );
                }

                return parameter_result_halt();
            }
        };
    }
}
