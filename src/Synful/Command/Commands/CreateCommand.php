<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;
use Synful\Templating\Template;

class CreateCommand extends Command
{
    /**
     * Construct the CreateMigration command.
     */
    public function __construct()
    {
        $this->name = 'cc';
        $this->description = 'Create a new command line command.';
        $this->required = false;
        $this->alias = 'create-command';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Command names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                $fileName = './src/App/Commands/'.$name.'.php';

                if (! file_exists($fileName)) {
                    $template = new Template('Command.tmpl', ['name' => $name], true);

                    file_put_contents(
                        $fileName,
                        $template->parse()
                    );
                    sf_info(
                        'Created command in \'src/App/Commands\' with name \''.$name.'\'.',
                        true
                    );
                    chmod($fileName, 0750);
                } else {
                    sf_error('Error: A command by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
