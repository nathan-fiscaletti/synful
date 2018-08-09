<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\Data\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class ListKeys extends Command
{
    /**
     * Construct the ListKeys command.
     */
    public function __construct()
    {
        $this->name = 'lk';
        $this->description = 'Outputs a list of all API Keys.';
        $this->required = false;
        $this->alias = 'list-keys';
        $this->exec = function () {
            sf_info('API Key List', true, false, false);
            sf_info('-----------------------------------------------------', true, false, false);
            $keys = APIKey::all();
            foreach ($keys as $key) {
                sf_info(
                    'Belongs To: '.sf_color($key->name, 'light_blue'),
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Auth Handle     : '.$key->auth,
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Whitelist-Only  : '.
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
                    '    Security        : '.sf_color('Level '.$key->security_level, 'light_green'),
                    true,
                    false,
                    false
                );
                $rate_limit = ($key->rate_limit == 0 && $key->rate_limit_seconds == 0)
                    ? sf_color('Unlimited', 'light_blue')
                    : sf_color(
                        $key->rate_limit.' Requests / '.$key->rate_limit_seconds.' seconds',
                        'light_green'
                    );
                sf_info(
                    '    Rate Limit      : '.
                    $rate_limit,
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Enabled         : '.
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

                if (in_array('*', $key->getRequestHandlersParsed())) {
                    $ep_str = sf_color('All', 'light_blue');
                } else {
                    $ep_access = json_decode($key->allowed_request_handlers, true);
                    $ep_str = '';
                    foreach ($ep_access as $ep) {
                        $ep_str .= sf_color($ep, 'light_green').', ';
                    }
                    $ep_str = sf_color('[ ', 'light_cyan').$ep_str.sf_color(']', 'light_cyan');
                }

                sf_info(
                    '    Endpoint Access : '.
                    $ep_str,
                    true,
                    false,
                    false
                );
                sf_info('', true, false, false);
            }

            return parameter_result_halt();
        };
    }
}
