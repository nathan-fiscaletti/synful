<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;
use Synful\Templating\Template;

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

                return parameter_result_halt();
            }

            if (! file_exists('./src/App/Data/Models/'.$name.'.php')) {
                $template = new Template('Model.tmpl', ['name' => $name, 'table' => $table], true);
                file_put_contents(
                    './src/App/Data/Models/'.$name.'.php',
                    $template->parse()
                );

                sf_info(
                    'Created database Model in \'src/App/Data/Models\' with name \''.$name.'\'.',
                    true
                );
                chmod('./src/App/Data/Models/'.$name.'.php', 0750);
                exec('composer dumpautoload >/dev/null 2>&1');
            } else {
                sf_error('Error: A Model by that name already exists.', true);
            }

            return parameter_result_halt();
        };
    }
}
