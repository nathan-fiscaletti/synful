<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Util\DataManagement\Models\APIKey;

class ListKeys extends Command
{
    /**
     * Construct the ListKeys command.
     */
    public function __construct()
    {
        $this->name = 'lk';
        $this->description = 'Outputs a list of all API Keys.';
        $this->required = false;
        $this->alias = 'list-keys';
        $this->exec = function () {
            sf_info('API Key List', true, false, false);
            sf_info('---------------------------------------------------', true, false, false);
            $sql_result = sf_sql('SELECT * FROM `api_keys` ORDER BY `is_master` DESC', [], true);
            while ($row = mysqli_fetch_assoc($sql_result)) {
                sf_info(
                    'Belongs To: '.sf_color($row['name'], 'light_blue'),
                    true,
                    false,
                    false
                );
                sf_info(
                    '    EMail / ID     : '.$row['email'].' / '.$row['id'],
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Whitelist-Only : '.
                    (($row['whitelist_only'])
                        ? sf_color(
                            'true',
                            'light_green'
                        )
                        : sf_color(
                            'false',
                            'light_red'
                        )
                    ),
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Is-Master      : '.
                    (($row['is_master'])
                        ? sf_color(
                            'true',
                            'light_green'
                        )
                        : sf_color(
                            'false',
                            'light_red'
                        )
                    ),
                    true,
                    false,
                    false
                );
                sf_info(
                    '    Enabled        : '.
                    (($row['enabled'])
                        ? sf_color(
                            'true',
                            'light_green'
                        )
                        : sf_color(
                            'false',
                            'light_red'
                        )
                    ),
                    true,
                    false,
                    false
                );
                sf_info('', true, false, false);
            }

            exit;
        };
    }
}