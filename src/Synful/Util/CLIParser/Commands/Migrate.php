<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;

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
                exit;
            }

            foreach (
                scandir('./src/Synful/Util/Data/Migrations') as $migration_file
            ) {
                if (
                    substr($migration_file, 0, 1) !== '.' && 
                    $migration_file != 'Util'
                ) {
                    include_once './src/Synful/Util/Data/Migrations/'.
                                 $migration_file;
                    $className = str_replace('.php', '', $migration_file);
                    
                    $fullClassName = '\\Synful\\Util\\Data\\Migrations\\'.
                                     $className;
                    $migration = new $fullClassName();

                    try {
                        if ($action == 'up') {
                            $migration->up();
                        } else if ($action == 'down') {
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
                scandir('./src/Synful/App/Data/Migrations') as $migration_file
            ) {
                if (substr($migration_file, 0, 1) !== '.') {
                    include_once './src/Synful/App/Data/Migrations/'.
                                 $migration_file;
                    $className = str_replace('.php', '', $migration_file);
                    $className = explode('_', $className)[1];
                    sf_info('Migrating '.$className.'...');
                    
                    $fullClassName = '\\Synful\\App\\Data\\Migrations\\'.
                                     $className;
                    $migration = new $fullClassName();

                    try {
                        if ($action == 'up') {
                            $migration->up();
                        } else if ($action == 'down') {
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

            exit;
        };
    }
}
