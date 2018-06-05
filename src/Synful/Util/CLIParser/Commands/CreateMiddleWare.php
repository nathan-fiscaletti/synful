<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

class CreateMiddleWare extends Command
{
    /**
     * Construct the CreateHandler command.
     */
    public function __construct()
    {
        $this->name = 'cmw';
        $this->description = 'Creates a new custom MiddleWare implementation.';
        $this->required = false;
        $this->alias = 'create-middleware';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: MiddleWare names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/Synful/App/MiddleWare/'.$name.'.php')) {
                    file_put_contents(
                        './src/Synful/App/MiddleWare/'.$name.'.php',
                        str_replace(
                            'MiddleWareName',
                            $name,
                            file_get_contents('./templates/MiddleWare.tmpl')
                        )
                    );

                    sf_info(
                        'Created MiddleWare in \'src/Synful/App/MiddleWare\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/Synful/App/MiddleWare/'.$name.'.php', 0750);
                    exec('composer dumpautoload >/dev/null 2>&1');
                } else {
                    sf_error('Error: MiddleWare by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
