<?php

namespace Synful\CLIParser\Commands;

use Synful\CLIParser\Commands\Util\Command;

class Migrate extends Command
{
    /**
     * Construct the Migrate command.
     */
    public function __construct()
    {
        $this->name = 'mi';
        $this->description = 'Run database migrations.';
        $this->required = false;
        $this->alias = 'migrate';

        $this->exec = function ($action) {
            if ($action != 'up' && $action != 'down') {
                sf_error('Usage: ./synful migrate [up | down]');

                return parameter_result_halt();
            }

            foreach (
                scandir('./src/Synful/Data/Migrations') as $migration_file
            ) {
                if (
                    substr($migration_file, 0, 1) !== '.' &&
                    $migration_file != 'Util'
                ) {
                    include_once './src/Synful/Data/Migrations/'.
                                 $migration_file;
                    $className = str_replace('.php', '', $migration_file);

                    $fullClassName = '\\Synful\\Data\\Migrations\\'.
                                     $className;
                    $migration = new $fullClassName();

                    try {
                        if ($action == 'up') {
                            $migration->up();
                        } elseif ($action == 'down') {
                            $migration->down();
                        }
                        sf_info(
                            'Migrating '.$className.'... ['.
                            sf_color('SUCCESS', 'light_green').
                            ']'
                        );
                    } catch (\Exception $e) {
                        sf_info(
                            'Migrating '.$className.'... ['.
                            sf_color('FAILURE', 'light_red').
                            ']'
                        );
                        sf_error($e->getMessage());
                    }
                }
            }

            foreach (
                scandir('./src/App/Data/Migrations') as $migration_file
            ) {
                if (substr($migration_file, 0, 1) !== '.') {
                    include_once './src/App/Data/Migrations/'.
                                 $migration_file;
                    $className = str_replace('.php', '', $migration_file);
                    $className = explode('_', $className)[1];

                    $fullClassName = '\\App\\Data\\Migrations\\'.
                                     $className;
                    $migration = new $fullClassName();

                    try {
                        if ($action == 'up') {
                            $migration->up();
                        } elseif ($action == 'down') {
                            $migration->down();
                        }
                        sf_info(
                            'Migrating '.$className.'... ['.
                            sf_color('SUCCESS', 'light_green').
                            ']'
                        );
                    } catch (\Exception $e) {
                        sf_info(
                            'Migrating '.$className.'... ['.
                            sf_color('FAILURE', 'light_red').
                            ']'
                        );
                        sf_error($e->getMessage());
                    }
                }
            }

            return parameter_result_halt();
        };
    }
}
