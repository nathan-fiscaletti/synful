<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

class CreateHandler extends Command
{
    /**
     * Construct the CreateHandler command.
     */
    public function __construct()
    {
        $this->name = 'ch';
        $this->description = 'Creates a request handler with the specified name in src/Synful/RequestHandlers.';
        $this->required = false;
        $this->alias = 'create-handler';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Request Handler names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/Synful/RequestHandlers/'.$name.'.php')) {
                    file_put_contents(
                        './src/Synful/RequestHandlers/'.$name.'.php',
                        str_replace('RequestHandlerName', $name, file_get_contents('./templates/RequestHandler.tmpl'))
                    );

                    sf_info(
                        'Created Request Handler in \'src/Synful/RequestHandlers\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/Synful/RequestHandlers/'.$name.'.php', 0700);
                    exec('chmod +x ./src/Synful/RequestHandlers/'.$name.'.php');
                    exec('composer dumpautoload');
                } else {
                    sf_error('Error: A request handler by that name already exists.', true);
                }
            }

            exit;
        };
    }
}
