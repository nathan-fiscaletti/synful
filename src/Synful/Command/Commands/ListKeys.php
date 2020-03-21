<?php

namespace Synful\Command\Commands;

use Ansi\Color16;
use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

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
            sf_info('API Key List', true, false);
            sf_info('-----------------------------------------------------', true, false);
            $keys = APIKey::all();
            foreach ($keys as $key) {
                sf_info(
                    'Belongs To: '.sf_color($key->name, Color16::FG_LIGHT_BLUE),
                    true,
                    false
                );
                sf_info(
                    '    Auth Handle     : '.$key->auth,
                    true,
                    false
                );
                sf_info(
                    '    Whitelist-Only  : '.
                    (($key->whitelist_only)
                        ? sf_color(
                            'true',
                            Color16::FG_LIGHT_GREEN
                        )
                        : sf_color(
                            'false',
                            Color16::FG_LIGHT_RED
                        )
                    ),
                    true,
                    false
                );
                sf_info(
                    '    Security        : '.sf_color('Level '.$key->security_level, Color16::FG_LIGHT_GREEN),
                    true,
                    false
                );
                $rate_limit = ($key->rate_limit == 0 && $key->rate_limit_seconds == 0)
                    ? sf_color('Unlimited', Color16::FG_LIGHT_BLUE)
                    : sf_color(
                        $key->rate_limit.' Requests / '.$key->rate_limit_seconds.' seconds',
                        Color16::FG_LIGHT_GREEN
                    );
                sf_info(
                    '    Rate Limit      : '.
                    $rate_limit,
                    true,
                    false
                );
                sf_info(
                    '    Enabled         : '.
                    (($key->enabled)
                        ? sf_color(
                            'true',
                            Color16::FG_LIGHT_GREEN
                        )
                        : sf_color(
                            'false',
                            Color16::FG_LIGHT_RED
                        )
                    ),
                    true,
                    false
                );

                if (in_array('*', $key->getRequestHandlersParsed())) {
                    $ep_str = sf_color('All', Color16::FG_LIGHT_BLUE);
                } else {
                    $ep_access = json_decode($key->allowed_request_handlers, true);
                    $ep_str = '';
                    foreach ($ep_access as $ep) {
                        $ep_str .= sf_color($ep, Color16::FG_LIGHT_GREEN).', ';
                    }
                    $ep_str = sf_color('[ ', 'light_cyan').$ep_str.sf_color(']', Color16::FG_LIGHT_CYAN);
                }

                sf_info(
                    '    Endpoint Access : '.
                    $ep_str,
                    true,
                    false
                );
                sf_info('', true, false);
            }

            return parameter_result_halt();
        };
    }
}
