<?php

namespace Synful\Command\Commands;

use Synful\Synful;
use Synful\Command\Commands\Util\Command;
use Synful\Templating\Template;

class CreateMigration extends Command
{
    /**
     * Construct the CreateMigration command.
     */
    public function __construct()
    {
        $this->name = 'cmi';
        $this->description = 'Create a database migration.';
        $this->required = false;
        $this->alias = 'create-migration';

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Migration names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                $fileName = './src/App/Data/Migrations/'.time().'_'.$name.'.php';

                if (! file_exists($fileName)) {
                    $template = new Template('Migration.tmpl', ['name' => $name], true);
                    file_put_contents(
                        $fileName,
                        $template->parse()
                    );

                    sf_info(
                        'Created Migration in \'src/Synful/Data/Migrations\' with name \''.$name.'\'.',
                        true
                    );
                    chmod($fileName, 0750);
                } else {
                    sf_error('Error: A Migration by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
