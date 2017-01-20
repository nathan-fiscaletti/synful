<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

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
        $this->exec = function ($email_or_id, $ip, $block_value) {
            $id = $email_or_id;
            $block = $block_value;
            if (! is_numeric($block)) {
                sf_error('Block value must be an integer value of either 1 or 0.', true, false, false);
            } else {
                if (APIKey::keyExists($id)) {
                    $key = APIKey::getKey($id);
                    $key->firewallIP($ip, $block);
                    $key->save();
                    sf_info(
                        'Set firewall on key \''.sf_color($id, 'light_blue').
                        '\' for ip \''.sf_color($ip, 'light_blue').'\' to \''.
                        (($block)
                            ? sf_color(
                                'true',
                                'light_green'
                            )
                            : sf_color(
                                'false',
                                'light_red'
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

            exit;
        };
    }
}
