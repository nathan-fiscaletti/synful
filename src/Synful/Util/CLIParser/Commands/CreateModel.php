<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Synful;
use Synful\Util\CLIParser\Commands\Util\Command;

class CreateModel extends Command
{
    /**
     * Construct the CreateModel command.
     */
    public function __construct()
    {
        $this->name = 'cm';
        $this->description = 'Create a database model.';
        $this->required = false;
        $this->alias = 'create-model';

        $this->exec = function ($name, $table) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            $table = trim($table);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Model names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );

                exit;
            }

            if (! file_exists('./src/Synful/App/Data/Models/'.$name.'.php')) {
                file_put_contents(
                    './src/Synful/App/Data/Models/'.$name.'.php',
                    str_replace(
                        '{table}',
                        $table,
                        str_replace(
                            '{name}',
                            $name,
                            file_get_contents('./templates/Model.tmpl')
                        )
                    )
                );

                sf_info(
                    'Created database Model in \'src/Synful/App/Data/Models\' with name \''.$name.'\'.',
                    true
                );
                chmod('./src/Synful/App/Data/Models/'.$name.'.php', 0750);
                exec('composer dumpautoload >/dev/null 2>&1');
            } else {
                sf_error('Error: A Model by that name already exists.', true);
            }

            exit;
        };
    }
}
