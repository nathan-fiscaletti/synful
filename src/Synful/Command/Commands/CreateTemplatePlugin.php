<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;
use Synful\Templating\Template;

class CreateTemplatePlugin extends Command
{
    /**
     * Construct the CreateHandler command.
     */
    public function __construct()
    {
        $this->name = 'ctp';
        $this->description = 'Creates a new custom Template plugin.';
        $this->required = false;
        $this->alias = 'create-template-plugin';

        $this->exec = function ($name, $key) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            $key = str_replace('_', '', $key);
            $key = str_replace(' ', '', $key);
            $key = trim($key);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Template plugin names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );

                return parameter_result_halt();
            }

            if (! file_exists('./src/App/Templating/Plugins/'.$name.'.php')) {
                $template = new Template('TemplatePlugin.tmpl', ['name' => $name, 'key' => $key], true);
                file_put_contents(
                    './src/App/Templating/Plugins/'.$name.'.php',
                    $template->parse()
                );

                sf_info(
                    'Created Template Plugin in \'src/App/Templating/Plugins\' with name \''.$name.'\'.',
                    true
                );
                chmod('./src/App/Templating/Plugins/'.$name.'.php', 0750);
                exec('composer dumpautoload >/dev/null 2>&1');
            } else {
                sf_error('Error: A template plugin by that name already exists.', true);
            }

            return parameter_result_halt();
        };
    }
}
