<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

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
                $fileName = './src/Synful/App/Commands/'.$name.'.php';

                if (! file_exists($fileName)) {
                    file_put_contents(
                        $fileName,
                        str_replace(
                            '{command}',
                            $name,
                            file_get_contents('./templates/Command.tmpl')
                        )
                    );

                    sf_info(
                        'Created command in \'src/Synful/App/Commands\' with name \''.$name.'\'.',
                        true
                    );
                    chmod($fileName, 0700);
                    exec('chmod +x '.$fileName);
                } else {
                    sf_error('Error: A command by that name already exists.', true);
                }
            }

            exit;
        };
    }
}
