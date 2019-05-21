<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class FirewallIp extends Command
{
    /**
     * Construct the FirewallIp command.
     */
    public function __construct()
    {
        $this->name = 'f';
        $this->description = 'Firewalls an IP Address on the specified key with the specified block value.';
        $this->required = false;
        $this->alias = 'firewall-ip';
        $this->exec = function ($auth, $ip, $block_value) {
            $block = $block_value;
            if (! is_numeric($block)) {
                sf_error('Block value must be an integer value of either 1 or 0.', true, false, false);
            } else {
                $key = APIKey::getApiKey($auth);
                if ($key !== null) {
                    $key->firewallIP($ip, $block);
                    $key->save();
                    sf_info(
                        'Set firewall on key \''.sf_color($auth, \Ansi\Color::FG_LIGHT_BLUE).
                        '\' for ip \''.sf_color($ip, \Ansi\Color::FG_LIGHT_BLUE).'\' to \''.
                        (($block)
                            ? sf_color(
                                'true',
                                \Ansi\Color::FG_LIGHT_GREEN
                            )
                            : sf_color(
                                'false',
                                \Ansi\Color::FG_LIGHT_RED
                            )
                        ).'\'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error('No key was found with that ID.', true, false, false);
                }
            }

            return parameter_result_halt();
        };
    }
}
