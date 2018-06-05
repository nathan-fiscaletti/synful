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
        $this->description = 'Creates a new custom Request Handler implementation.';
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
                if (! file_exists('./src/Synful/App/RequestHandlers/'.$name.'.php')) {
                    file_put_contents(
                        './src/Synful/App/RequestHandlers/'.$name.'.php',
                        str_replace(
                            'EndPoint',
                            strtolower($name),
                            str_replace(
                                'RequestHandlerName',
                                $name,
                                file_get_contents('./templates/RequestHandler.tmpl')
                            )
                        )
                    );

                    sf_info(
                        'Created Request Handler in \'src/Synful/App/RequestHandlers\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/Synful/App/RequestHandlers/'.$name.'.php', 0750);
                    exec('composer dumpautoload >/dev/null 2>&1');
                } else {
                    sf_error('Error: A request handler by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
