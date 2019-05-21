<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;
use Synful\Templating\Template;

class CreateMiddleware extends Command
{
    /**
     * Construct the CreateHandler command.
     */
    public function __construct()
    {
        $this->name = 'cmw';
        $this->description = 'Creates a new custom Middleware implementation.';
        $this->required = false;
        $this->alias = 'create-middleware';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Middleware names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/App/Middleware/'.$name.'.php')) {
                    $template = new Template('Middleware.tmpl', ['name' => $name], true);
                    file_put_contents(
                        './src/App/Middleware/'.$name.'.php',
                        $template->parse()
                    );

                    sf_info(
                        'Created Middleware in \'src/App/Middleware\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/App/Middleware/'.$name.'.php', 0750);
                    exec('composer dumpautoload >/dev/null 2>&1');
                } else {
                    sf_error('Error: Middleware by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
