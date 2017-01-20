<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

class RemoveKey extends Command
{
	/**
	 * Construct the RemoveKey command.
	 */
	public function __construct()
	{
		$this->name = 'rk';
		$this->description = 'Removes a key from the System based on email or ID.';
		$this->required = false;
		$this->alias = 'remove-key';
		$this->exec = function ($email_or_id) {
			if (APIKey::keyExists($email_or_id)) {
	            $key = APIKey::getKey($email_or_id);
	            $key->delete();
	            sf_info(
	                'APIKey for ID \''.sf_color($email_or_id, 'light_blue').
	                '\' has been '.sf_color('removed', 'light_red').'.',
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