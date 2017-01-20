<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

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

		$this->exec = function ($email_or_id) {
			if (APIKey::keyExists($email_or_id)) {
	            $key = APIKey::getKey($email_or_id);

	            foreach ($key->ip_firewall as $firewall_entry) {
	                sf_info(
	                    'IP: '.sf_color($firewall_entry['ip'], 'yellow').' is '.
	                    (($firewall_entry['block'])
	                        ? sf_color(
	                            'blocked',
	                            'light_red'
	                        )
	                        : sf_color(
	                            'allowed',
	                            'light_green'
	                        )
	                    ).
	                    ' for key '.sf_color($email_or_id, 'light_cyan'),
	                    true,
	                    false,
	                    false
	                );
	            }
	        } else {
	            sf_error('No key was found with that ID.', true, false, false);
	        }

            exit;
		};
	}
}