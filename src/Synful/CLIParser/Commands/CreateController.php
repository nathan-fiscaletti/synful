<?php

namespace Synful\CLIParser\Commands;

use Synful\CLIParser\Commands\Util\Command;
use Synful\Templating\Template;

class CreateController extends Command
{
    /**
     * Construct the CreateController command.
     */
    public function __construct()
    {
        $this->name = 'cct';
        $this->description = 'Creates a new custom Controller implementation.';
        $this->required = false;
        $this->alias = 'create-controller';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Controller names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/App/Controllers/'.$name.'.php')) {
                    $template = new Template('Controller.tmpl', ['name' => $name], true);

                    file_put_contents(
                        './src/App/Controllers/'.$name.'.php',
                        $template->parse()
                    );

                    sf_info(
                        'Created new Controller in \'src/App/Controllers\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/App/Controllers/'.$name.'.php', 0750);
                    exec('composer dumpautoload >/dev/null 2>&1');
                } else {
                    sf_error('Error: A Controller by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
