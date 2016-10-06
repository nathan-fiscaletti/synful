<?php

namespace Synful\CLIParser;

use Synful\Synful;
use Synful\DataManagement\Models\APIKey;

/**
 * Store handlers for CLI Parameters in this file.
 */
class CLIHandlers
{
    /**
     * Handles listsql CLI Parameter.
     *
     * @param  string $value
     */
    public static function listSql($value)
    {
        sf_info(
            'Sql Server List',
            true,
            false,
            false
        );
        sf_info(
            '---------------------------------------------------',
            true,
            false,
            false
        );
        foreach (sf_conf('sqlservers') as $server_name => $server) {
            sf_info(
                ' | '.sf_color($server_name, 'light_green'),
                true,
                false,
                false
            );
            sf_info(
                ' --------------------------------------------------',
                true,
                false,
                false
            );
            sf_info(
                ' | Server Info  : ['.$server['host'].', '.$server['port'].']',
                true,
                false,
                false
            );
            $dbs = ' | Databases    : ';
            $database_info = '';
            foreach ($server['databases'] as $database_name => $database) {
                $database_info .= ($database_info == '') ? '['.$database_name : ', '.$database_name;
            }
            sf_info(
                $dbs.$database_info.']',
                true,
                false,
                false
            );
            sf_info(
                '---------------------------------------------------',
                true,
                false,
                false
            );
        }
        exit(0);
    }

    /**
     * Handles whitelistonly CLI Parameter.
     *
     * @param  string $value
     */
    public static function whiteListOnly($value)
    {
        $param_data = explode(',', $value);
        if (count($param_data) < 2 || ! ($param_data[1] === 'true' || $param_data[1] === 'false')) {
            sf_error(
                'Unable to set White-List Only.',
                false,
                false,
                false
            );
            sf_error(
                'Please provide the data in the format \'<email/id>,<true/false>\'',
                false,
                false,
                false
            );
            sf_error(
                'Example: php synful.php whitelistonly=jon@acme.com,true',
                false,
                false,
                false
            );
            exit();
        } else {
            if (! APIKey::keyExists($param_data[0])) {
                sf_error(
                    'No key found for email/ID \''.$param_data[0].'\'.',
                    true,
                    false,
                    false
                );
                exit(2);
            } else {
                $key = APIKey::getKey($param_data[0]);
                $key->whitelist_only = ($param_data[1] === 'true') ? 1 : 0;
                $key->save();
                sf_info(
                    'Key \''.sf_color($param_data[0], 'light_green').
                    '\' updated with new White-List Only value \''.
                    (($param_data[1] == 'true')
                        ? sf_color(
                            $param_data[1],
                            'light_green'
                        )
                        : sf_color(
                            $param_data[1],
                            'light_red'
                        )
                    ).'\'.',
                    true,
                    false,
                    false
                );
                exit();
            }
        }
    }

    /**
     * Handle showfirewall CLI Parameter.
     *
     * @param  string $value
     */
    public static function showFireWall($value)
    {
        if (APIKey::keyExists($value)) {
            $key = APIKey::getKey($value);

            foreach ($key->ip_firewall as $firewall_entry) {
                sf_info(
                    'IP: '.sf_color($firewall_entry['ip'], 'yellow').' is '.
                    (($firewall_entry['block'])
                        ? sf_color(
                            'blocked',
                            'light_red'
                        )
                        : sf_color(
                            'allowed',
                            'light_green'
                        )
                    ).
                    ' for key '.sf_color($value, 'light_cyan'),
                    true,
                    false,
                    false
                );
            }

            exit();
        } else {
            sf_error('No key was found with that ID.', true, false, false);
            exit();
        }
    }

    /**
     * Handle firewallip CLI Parameter.
     *
     * @param  string $value
     */
    public static function fireWallIp($value)
    {
        $firewall_data = explode(',', $value);
        if (count($firewall_data) < 3) {
            sf_error(
                'Unable to firewall IP.',
                true,
                false,
                false
            );
            sf_error(
                'Please provide the key data in the format \'<email/id>,<ip>,<block_value>\'',
                true,
                false,
                false
            );
            sf_error(
                'Example: php synful.php firewallip=jon@acme.com,192.168.1.1,1',
                true,
                false,
                false
            );
            exit(2);
        } else {
            $id = $firewall_data[0];
            $ip = $firewall_data[1];
            $block = $firewall_data[2];
            if (APIKey::keyExists($id)) {
                $key = APIKey::getKey($id);
                $key->firewallIP($ip, $block);
                $key->save();
                sf_info(
                    'Set firewall on key \''.sf_color($id, 'light_blue').
                    '\' for ip \''.sf_color($ip, 'light_blue').'\' to \''.
                    (($block)
                        ? sf_color(
                            'true',
                            'light_green'
                        )
                        : sf_color(
                            'false',
                            'light_red'
                        )
                    ).'\'.',
                    true,
                    false,
                    false
                );
                exit(0);
            } else {
                sf_error('No key was found with that ID.', true, false, false);
                exit(2);
            }
        }
    }

    /**
     * Handle unfirewallip CLI Parameter.
     *
     * @param  string $value
     */
    public static function unFireWallIp($value)
    {
        $firewall_data = explode(',', $value);
        if (count($firewall_data) < 2) {
            sf_error(
                'Unable to unfirewall IP.',
                true,
                false,
                false
            );
            sf_error(
                'Please provide the key data in the format \'<email/id>,<ip>\'',
                true,
                false,
                false
            );
            sf_error(
                'Example: php synful.php unfirewallip=jon@acme.com,192.168.1.1',
                true,
                false,
                false
            );
            exit(2);
        } else {
            $id = $firewall_data[0];
            $ip = $firewall_data[1];
            if (APIKey::keyExists($id)) {
                $key = APIKey::getKey($id);
                if ($key->isFirewalled($ip)) {
                    $key->unfirewallIP($ip);
                    $key->save();
                    sf_info(
                        'Removed firewall entry on key \''.sf_color($id, 'light_blue').
                        '\' for ip \''.sf_color($ip, 'light_blue').'\'.',
                        true,
                        false,
                        false
                    );
                    exit(0);
                } else {
                    sf_error(
                        'That IP does not have a firewall entry on that key.',
                        true,
                        false,
                        false
                    );
                    exit(2);
                }
            } else {
                sf_error(
                    'No key was found with that ID.',
                    true,
                    false,
                    false
                );
                exit(2);
            }
        }
    }

    /**
     * Handle disablekey CLI Parameter.
     *
     * @param  string $value
     */
    public static function disableKey($value)
    {
        if (APIKey::keyExists($value)) {
            $key = APIKey::getKey($value);
            $key->enabled = false;
            $key->save();
            sf_info(
                'APIKey for ID \''.sf_color($value, 'light_blue').
                '\' has been '.sf_color('disabled', 'light_red').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            sf_error('No key was found with that ID.', true, false, false);
            exit(2);
        }
    }

    /**
     * Handle enablekey CLI Parameter.
     *
     * @param  string $value
     */
    public static function enableKey($value)
    {
        if (APIKey::keyExists($value)) {
            $key = APIKey::getKey($value);
            $key->enabled = true;
            $key->save();
            sf_info(
                'APIKey for ID \''.sf_color($value, 'light_blue').
                '\' has been '.sf_color('enabled', 'light_green').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            sf_error('No key was found with that ID.', true, false, false);
            exit(2);
        }
    }

    /**
     * Handle removekey CLI Parameter.
     *
     * @param  string $value
     */
    public static function removeKey($value)
    {
        if (APIKey::keyExists($value)) {
            $key = APIKey::getKey($value);
            $key->delete();
            sf_info(
                'APIKey for ID \''.sf_color($value, 'light_blue').
                '\' has been '.sf_color('removed', 'light_red').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            sf_error('No key was found with that ID.', true, false, false);
            exit(2);
        }
    }

    /**
     * Handle listkeys CLI Parameter.
     *
     * @param  string $value
     */
    public static function listKeys($value)
    {
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
        exit(0);
    }

    /**
     * Handle createkey CLI Parameter.
     *
     * @param  string $value
     */
    public static function createKey($value)
    {
        $new_key_data = explode(',', $value);
        if (count($new_key_data) < 3) {
            sf_error('Unable to create new API Key.', true, false, false);
            sf_error(
                'Please provide the key data in the format \'<email>,<First_Last>,<whitelist_only_as_int>\'',
                true,
                false,
                false
            );
            sf_error(
                'Example: php synful.php createkey=jon@acme.com,John_Doe,0',
                true,
                false,
                false
            );
            exit(2);
        } else {
            $email = $new_key_data[0];
            $name = str_replace('_', ' ', $new_key_data[1]);
            $whitelist_only = intval($new_key_data[2]);

            if (! is_int($whitelist_only)) {
                sf_error('Unable to create new API Key.', true, false, false);
                sf_error(
                    'Please provide the key data in the format \'<email>,<First_Last>,<whitelist_only_as_int>\'',
                    true,
                    false,
                    false
                );
                sf_error(
                    'Example: php synful.php createkey=jon@acme.com,John_Doe,0',
                    true,
                    false,
                    false
                );
                exit(2);
            }

            if (APIKey::keyExists($email)) {
                sf_error('A key with that email is already defined.', true, false, false);
                exit(2);
            }

            sf_info('Creating new key with data: ', true, false, false);
            sf_info('    Name: '.$name, true, false, false);
            sf_info('    Email: '.$email, true, false, false);

            sf_info('------------------------------------------------', true, false, false);

            if (APIKey::addNew($name, $email, $whitelist_only, 0, true) == null) {
                sf_error(
                    'There was an error while creating your new API Key.',
                    true,
                    false,
                    false
                );
            }

            exit(2);
        }
    }

    /**
     * Handle createhandler CLI Parameter.
     *
     * @param  string $value
     */
    public static function createHandler($value)
    {
        $value = str_replace('_', '', $value);
        $value = trim($value);

        if (! ctype_alpha($value)) {
            sf_error(
                'Error: Request Handler names must only contain alphabetic characters and no spaces. '.
                'TitleCase recommended.',
                true
            );
            exit(0);
        } else {
            if (! file_exists('./src/Synful/RequestHandlers/'.$value.'.php')) {
                file_put_contents(
                    './src/Synful/RequestHandlers/'.$value.'.php',
                    str_replace('RequestHandlerName', $value, file_get_contents('./templates/RequestHandler.tmpl'))
                );

                sf_info(
                    'Created Request Handler in \'src/Synful/RequestHandlers\' with name \''.$value.'\'.',
                    true
                );
                chmod('./src/Synful/RequestHandlers/'.$value.'.php', 0700);
                exec('chmod +x ./src/Synful/RequestHandlers/'.$value.'.php');
                exec('php composer.phar dumpautoload');
                exit(0);
            } else {
                sf_error('Error: A request handler by that name already exists.', true);
                exit(0);
            }
        }
    }

    /**
     * Handle standalone CLI Parameter.
     *
     * @param bool $value
     */
    public static function standAlone($value)
    {
        Synful::$config->set('system.standalone', ($value == null) ? true : json_decode($value));
        $str = (sf_conf('system.standalone')) ? 'true' : 'false';
        sf_note('CONFIG: Set standalone mode to \''.$str.'\'.');
    }

    /**
     * Handle color CLI Parameter.
     *
     * @param bool $value
     */
    public static function enableColor($value)
    {
        Synful::$config->set('system.color', ($value == null) ? true : json_decode($value));
        $str = (sf_conf('system.color')) ? 'true' : 'false';
        sf_note('CONFIG: Set console color to \''.$str.'\'.');
    }
}
