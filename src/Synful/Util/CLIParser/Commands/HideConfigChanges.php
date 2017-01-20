<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Synful;

class HideConfigChanges extends Command
{
	/**
	 * Construct the Color command.
	 */
	public function __construct()
	{
		$this->name = 'hc';
		$this->description = 'Used to hide config change messages on initialization.';
		$this->required = false;
		$this->alias = 'hide-config';

		$this->exec = function ($bool) {
			return $bool;
		};
	}
}