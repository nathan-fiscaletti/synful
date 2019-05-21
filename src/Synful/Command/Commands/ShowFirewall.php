<?php

namespace Synful\Command\Commands;

use Synful\Data\Models\APIKey;
use Synful\Command\Commands\Util\Command;

class ShowFirewall extends Command
{
    /**
     * Construct the ShowFirewall command.
     */
    public function __construct()
    {
        $this->name = 'sf';
        $this->description = 'Lists firewall entries for a specific key.';
        $this->required = false;
        $this->alias = 'show-firewall';

        $this->exec = function ($auth) {
            $key = APIKey::getApiKey($auth);
            if ($key !== null) {
                foreach ($key->firewall()->get() as $firewall_entry) {
                    sf_info(
                        'IP: '.sf_color($firewall_entry->ip, \Ansi\Color::FG_YELLOW).' is '.
                        (($firewall_entry->block)
                            ? sf_color(
                                'blocked',
                                \Ansi\Color::FG_LIGHT_RED
                            )
                            : sf_color(
                                'allowed',
                                \Ansi\Color::FG_LIGHT_GREEN
                            )
                        ).
                        ' for key '.sf_color($auth, \Ansi\Color::FG_LIGHT_CYAN),
                        true,
                        false,
                        false
                    );
                }
            } else {
                sf_error('No key was found with that ID.', true, false, false);
            }

            return parameter_result_halt();
        };
    }
}
