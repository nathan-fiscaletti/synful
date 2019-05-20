<?php

namespace Synful\Command\Commands;

use Synful\Command\Commands\Util\Command;

class CreateSerializer extends Command
{
    /**
     * Construct the CreateHandler command.
     */
    public function __construct()
    {
        $this->name = 'cs';
        $this->description = 'Creates a new custom Serializer implementation.';
        $this->required = false;
        $this->alias = 'create-serializer';

        $this->exec = function ($name, $contentType) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Serializer names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/App/Serializers/'.$name.'.php')) {
                    $template = new Template('Serializer.tmpl', ['name' => $name, 'mimetype' => $contentType], true);
                    file_put_contents(
                        './src/App/Serializers/'.$name.'.php',
                        $template->parse()
                    );

                    sf_info(
                        'Created Serializer in \'src/App/Serializers\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/App/Serializers/'.$name.'.php', 0750);
                    exec('composer dumpautoload >/dev/null 2>&1');
                } else {
                    sf_error('Error: Serializer by that name already exists.', true);
                }
            }

            return parameter_result_halt();
        };
    }
}
