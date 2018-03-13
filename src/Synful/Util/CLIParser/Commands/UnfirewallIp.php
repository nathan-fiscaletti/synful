<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\DataManagement\Models\APIKey;
use Synful\Util\CLIParser\Commands\Util\Command;

class UnfirewallIp extends Command
{
    /**
     * Construct the UnfirewallIp command.
     */
    public function __construct()
    {
        $this->name = 'uf';
        $this->description = 'Removes the firewall entry for the specified ip on the specified key.';
        $this->required = false;

        $this->alias = 'unfirewall-ip';
        $this->exec = function ($auth_or_id, $ip) {
            $id = $auth_or_id;
            $key = APIKey::getKey($id);
            if ($key !== null) {
                if ($key->isFirewalled($ip)) {
                    $key->unfirewallIP($ip);
                    $key->save();
                    sf_info(
                        'Removed firewall entry on key \''.sf_color($id, 'light_blue').
                        '\' for ip \''.sf_color($ip, 'light_blue').'\'.',
                        true,
                        false,
                        false
                    );
                } else {
                    sf_error(
                        'That IP does not have a firewall entry on that key.',
                        true,
                        false,
                        false
                    );
                }
            } else {
                sf_error(
                    'No key was found with that ID.',
                    true,
                    false,
                    false
                );
            }

            exit;
        };
    }
}
