<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

class EnableKey extends Command
{
	/**
	 * Construct the EnableKey command.
	 */
	public function __construct()
	{
		$this->name = 'ek';
		$this->description = 'Enables a key that has been disabled based on email or ID.';
		$this->required = false;
		$this->alias = 'enable-key';
		$this->exec = function ($email_or_id) {
			if (APIKey::keyExists($email_or_id)) {
	            $key = APIKey::getKey($email_or_id);
	            $key->enabled = true;
	            $key->save();
	            sf_info(
	                'APIKey for ID \''.sf_color($email_or_id, 'light_blue').
	                '\' has been '.sf_color('enabled', 'light_green').'.',
	                true,
	                false,
	                false
	            );
	        } else {
	            sf_error('No key was found with that ID.', true, false, false);
	        }

	        exit;
		};
	}
}