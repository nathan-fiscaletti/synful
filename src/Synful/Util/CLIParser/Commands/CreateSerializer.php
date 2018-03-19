<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

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

        $this->exec = function ($name) {
            $name = str_replace('_', '', $name);
            $name = trim($name);

            if (! ctype_alpha($name)) {
                sf_error(
                    'Error: Serializer names must only contain alphabetic characters and no spaces. '.
                    'TitleCase recommended.',
                    true
                );
            } else {
                if (! file_exists('./src/Synful/App/Serializers/'.$name.'.php')) {
                    file_put_contents(
                        './src/Synful/App/Serializers/'.$name.'.php',
                        str_replace(
                            'SerializerName',
                            $name,
                            file_get_contents('./templates/Serializer.tmpl')
                        )
                    );

                    sf_info(
                        'Created Serializer in \'src/Synful/App/Serializers\' with name \''.$name.'\'.',
                        true
                    );
                    chmod('./src/Synful/App/Serializers/'.$name.'.php', 0700);
                    exec('chmod +x ./src/Synful/App/Serializers/'.$name.'.php');
                    exec('composer dumpautoload');
                } else {
                    sf_error('Error: Serializer by that name already exists.', true);
                }
            }

            exit;
        };
    }
}
