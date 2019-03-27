<?php

namespace Synful\CLIParser\Commands;

use Synful\CLIParser\Commands\Util\Command;

class Register extends Command
{
    /**
     * Construct the new command.
     */
    public function __construct()
    {
        $this->name = 'reg';
        $this->description = 'Register a Command or Request Handler with the system.';
        $this->required = false;
        $this->alias = 'register';

        $this->exec = function ($type, $name) {
            $type = strtolower($type);

            // Validate the file
            if (! $this->validate($type, $name)) {
                return parameter_result_halt();
            }

            // Determine register type
            if ($type == 'command') {
                $data = sf_json_decode(file_get_contents('./config/CommandLine.json'), true);

                if (in_array('App\\Commands\\'.$name, $data['commands'])) {
                    sf_error('That Command is already registered.', true, false, false);

                    return parameter_result_halt();
                }

                $data['commands'][] = 'App\\Commands\\'.$name;
                $data = json_encode($data, JSON_PRETTY_PRINT);

                $data = str_replace(["\n\r", "\n", "\r"], "\n", $data);
                $data = str_replace(
                    "{\n",
                    "{\n\n    /*\n     |--------------------------------------".
                    "------------------------------------\n     | Commands\n  ".
                    '   |----------------------------------------------------'.
                    "----------------------\n     |\n     | The CLI Commands ".
                    "to register with the System.\n     */\n\n",
                    $data
                );

                file_put_contents('./config/CommandLine.json', $data);
                sf_info('Registered: '.$name.' as Command.', true, false, false);

                return parameter_result_halt();
            } elseif ($type == 'requesthandler') {
                $data = sf_json_decode(file_get_contents('./config/RequestHandlers.json'), true);

                if (in_array('Synful\\App\\RequestHandlers\\'.$name, $data['registered'])) {
                    sf_error('That RequestHandler is already registered.', true, false, false);

                    return parameter_result_halt();
                }

                $data['registered'][] = 'Synful\\App\\RequestHandlers\\'.$name;
                $data = json_encode($data, JSON_PRETTY_PRINT);
                $data = str_replace(["\n\r", "\n", "\r"], "\n", $data);
                $data = str_replace(
                    "{\n",
                    "{\n\n    /*\n     |---------------------------------------".
                    "-----------------------------------\n     | Registered\n  ".
                    '   |----------------------------------------------------'.
                    "----------------------\n     |\n     | The ".
                    "RequestHandlers to register in the System.\n     */\n\n",
                    $data
                );

                file_put_contents('./config/RequestHandlers.json', $data);
                sf_info('Registered: '.$name.' as RequestHandler.', true, false, false);

                return parameter_result_halt();
            }
        };
    }

    /**
     * Validate the file we are trying to register.
     *
     * @param  string $type
     * @param  string $name
     *
     * @return bool
     */
    private function validate($type, $name)
    {
        if ($type != 'command' && $type != 'requesthandler') {
            sf_error(
                'Invalid type passed to Register command. '.
                'Valid types [ \'command\', \'requesthandler\' ].',
                true,
                false,
                false
            );

            return false;
        }

        if ($type == 'command') {
            if (! file_exists('./src/App/Commands/'.$name.'.php')) {
                sf_error(
                    'Invalid name passed to Register command. File missing.',
                    true,
                    false,
                    false
                );

                return false;
            }
        } elseif ($type == 'requesthandler') {
            if (! file_exists('./src/App/RequestHandlers/'.$name.'.php')) {
                sf_error(
                    'Invalid name passed to Register command. File missing.',
                    true,
                    false,
                    false
                );

                return false;
            }
        }

        return true;
    }
}
