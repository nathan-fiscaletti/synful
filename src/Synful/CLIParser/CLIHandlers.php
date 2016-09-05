<?php

namespace Synful\CLIParser;

use Synful\Synful;
use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;
use Synful\DataManagement\Models\APIKey;
use Synful\Colors;

/**
 * Store handlers for CLI Parameters in this file.
 */
class CLIHandlers
{
    /**
     * Handles whitelistonly CLI Parameter.
     *
     * @param  string $value
     */
    public static function whiteListOnly($value)
    {
        $param_data = explode(',', $value);
        if (count($param_data) < 2 || ! ($param_data[1] === 'true' || $param_data[1] === 'false')) {
            IOFunctions::out(
                LogLevel::ERRO,
                'Unable to set White-List Only.',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
                'Please provide the data in the format \'<email/id>,<true/false>\'',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
                'Example: php synful.php whitelistonly=jon@acme.com,true',
                true,
                false,
                false
            );
            exit(2);
        } else {
            if (! APIKey::keyExists($param_data[0])) {
                IOFunctions::out(
                    LogLevel::ERRO,
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
                IOFunctions::out(
                    LogLevel::INFO,
                    'Key \''.Colors::cs($param_data[0], 'light_green').
                    '\' updated with new White-List Only value \''.
                    (($param_data[1] == 'true')
                        ? Colors::cs(
                            $param_data[1],
                            'light_green'
                        )
                        : Colors::cs(
                            $param_data[1],
                            'light_red'
                        )
                    ).'\'.',
                    true,
                    false,
                    false
                );
                exit(0);
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
                IOFunctions::out(
                    LogLevel::INFO,
                    'IP: '.Colors::cs($firewall_entry['ip'], 'yellow').' is '.
                    (($firewall_entry['block'])
                        ? Colors::cs(
                            'blocked',
                            'light_red'
                        )
                        : Colors::cs(
                            'allowed',
                            'light_green'
                        )
                    ).
                    ' for key '.Colors::cs($value, 'light_cyan'),
                    true,
                    false,
                    false
                );
            }

            exit(0);
        } else {
            IOFunctions::out(LogLevel::ERRO, 'No key was found with that ID.', true, false, false);
            exit(2);
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
            IOFunctions::out(
                LogLevel::ERRO,
                'Unable to firewall IP.',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
                'Please provide the key data in the format \'<email/id>,<ip>,<block_value>\'',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
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
                IOFunctions::out(
                    LogLevel::INFO,
                    'Set firewall on key \''.Colors::cs($id, 'light_blue').
                    '\' for ip \''.Colors::cs($ip, 'light_blue').'\' to \''.
                    (($block)
                        ? Colors::cs(
                            'true',
                            'light_green'
                        )
                        : Colors::cs(
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
                IOFunctions::out(LogLevel::ERRO, 'No key was found with that ID.', true, false, false);
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
            IOFunctions::out(
                LogLevel::ERRO,
                'Unable to unfirewall IP.',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
                'Please provide the key data in the format \'<email/id>,<ip>\'',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
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
                    IOFunctions::out(
                        LogLevel::INFO,
                        'Removed firewall entry on key \''.Colors::cs($id, 'light_blue').
                        '\' for ip \''.Colors::cs($ip, 'light_blue').'\'.',
                        true,
                        false,
                        false
                    );
                    exit(0);
                } else {
                    IOFunctions::out(
                        LogLevel::ERRO,
                        'That IP does not have a firewall entry on that key.',
                        true,
                        false,
                        false
                    );
                    exit(2);
                }
            } else {
                IOFunctions::out(
                    LogLevel::ERRO,
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
            IOFunctions::out(
                LogLevel::INFO,
                'APIKey for ID \''.Colors::cs($value, 'light_blue').
                '\' has been '.Colors::cs('disabled', 'light_red').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            IOFunctions::out(LogLevel::ERRO, 'No key was found with that ID.', true, false, false);
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
            IOFunctions::out(
                LogLevel::INFO,
                'APIKey for ID \''.Colors::cs($value, 'light_blue').
                '\' has been '.Colors::cs('enabled', 'light_green').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            IOFunctions::out(LogLevel::ERRO, 'No key was found with that ID.', true, false, false);
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
            IOFunctions::out(
                LogLevel::INFO,
                'APIKey for ID \''.Colors::cs($value, 'light_blue').
                '\' has been '.Colors::cs('removed', 'light_red').'.',
                true,
                false,
                false
            );
            exit(0);
        } else {
            IOFunctions::out(LogLevel::ERRO, 'No key was found with that ID.', true, false, false);
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
        IOFunctions::out(LogLevel::INFO, 'API Key List', true, false, false);
        IOFunctions::out(LogLevel::INFO, '---------------------------------------------------', true, false, false);
        $sql_result = Synful::$sql->executeSql('SELECT * FROM `api_keys` ORDER BY `is_master` DESC', [], true);
        while ($row = mysqli_fetch_assoc($sql_result)) {
            IOFunctions::out(
                LogLevel::INFO,
                'Belongs To: '.Colors::cs($row['name'], 'light_blue'),
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::INFO,
                '    EMail / ID     : '.$row['email'].' / '.$row['id'],
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::INFO,
                '    Whitelist-Only : '.
                (($row['whitelist_only'])
                    ? Colors::cs(
                        'true',
                        'light_green'
                    )
                    : Colors::cs(
                        'false',
                        'light_red'
                    )
                ),
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::INFO,
                '    Is-Master      : '.
                (($row['is_master'])
                    ? Colors::cs(
                        'true',
                        'light_green'
                    )
                    : Colors::cs(
                        'false',
                        'light_red'
                    )
                ),
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::INFO,
                '    Enabled        : '.
                (($row['enabled'])
                    ? Colors::cs(
                        'true',
                        'light_green'
                    )
                    : Colors::cs(
                        'false',
                        'light_red'
                    )
                ),
                true,
                false,
                false
            );
            IOFunctions::out(LogLevel::INFO, '', true, false, false);
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
            IOFunctions::out(LogLevel::ERRO, 'Unable to create new API Key.', true, false, false);
            IOFunctions::out(
                LogLevel::ERRO,
                'Please provide the key data in the format \'<email>,<First_Last>,<whitelist_only_as_int>\'',
                true,
                false,
                false
            );
            IOFunctions::out(
                LogLevel::ERRO,
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
                IOFunctions::out(LogLevel::ERRO, 'Unable to create new API Key.', true, false, false);
                IOFunctions::out(
                    LogLevel::ERRO,
                    'Please provide the key data in the format \'<email>,<First_Last>,<whitelist_only_as_int>\'',
                    true,
                    false,
                    false
                );
                IOFunctions::out(
                    LogLevel::ERRO,
                    'Example: php synful.php createkey=jon@acme.com,John_Doe,0',
                    true,
                    false,
                    false
                );
                exit(2);
            }

            if (APIKey::keyExists($email)) {
                IOFunctions::out(LogLevel::ERRO, 'A key with that email is already defined.', true, false, false);
                exit(2);
            }

            IOFunctions::out(LogLevel::INFO, 'Creating new key with data: ', true, false, false);
            IOFunctions::out(LogLevel::INFO, '    Name: '.$name, true, false, false);
            IOFunctions::out(LogLevel::INFO, '    Email: '.$email, true, false, false);

            IOFunctions::out(LogLevel::INFO, '------------------------------------------------', true, false, false);

            if (APIKey::addNew($name, $email, $whitelist_only, 0, true) == null) {
                IOFunctions::out(
                    LogLevel::ERRO,
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
            IOFunctions::out(
                LogLevel::ERRO,
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

                IOFunctions::out(
                    LogLevel::INFO,
                    'Created Request Handler in \'src/Synful/RequestHandlers\' with name \''.$value.'\'.',
                    true
                );
                chmod('./src/Synful/RequestHandlers/'.$value.'.php', 0700);
                exec('chmod +x./src/Synful/RequestHandlers/'.$value.'.php');
                exec('php composer.phar dumpautoload');
                exit(0);
            } else {
                IOFunctions::out(LogLevel::ERRO, 'Error: A request handler by that name already exists.', true);
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
        Synful::$config['system']['standalone'] = ($value == null) ? true : json_decode($value);
        $str = (Synful::$config['system']['standalone']) ? 'true' : 'false';
        IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set standalone mode to \''.$str.'\'.');
    }

    /**
     * Handle logfile CLI Parameter.
     *
     * @param string $value
     */
    public static function logFile($value)
    {
        if ($value != null) {
            Synful::$config['files']['logfile'] = $value;
            IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set logfile to \''.$value.'\'.');
        } else {
            IOFunctions::out(LogLevel::WARN, 'Invalid logfile defined. Using default.');
        }
    }

    /**
     * Handle ip CLI Parameter.
     *
     * @param string $value
     */
    public static function listenIp($value)
    {
        if ($value != null) {
            if (! filter_var($ip, FILTER_VALIDATE_IP) === false) {
                Synful::$config['system']['ip'] = $value;
                IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set IP to \''.$value.'\'.');
            } else {
                IOFunctions::out(LogLevel::WARN, 'Invalid IP defined. Using default.');
            }
        } else {
            IOFunctions::out(LogLevel::WARN, 'Invalid IP defined. Using default.');
        }
    }

    /**
     * Handle port CLI Parameter.
     *
     * @param int $value
     */
    public static function listenPort($value)
    {
        if ($value != null) {
            Synful::$config['system']['port'] = $value;
            IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set port to \''.$value.'\'.');
        } else {
            IOFunctions::out(LogLevel::WARN, 'Invalid port defined. Using default.');
        }
    }

    /**
     * Handle multithread CLI Parameter.
     *
     * @param bool $value
     */
    public static function multiThread($value)
    {
        Synful::$config['system']['multithread'] = ($value == null) ? true : json_decode($value);
        $str = (Synful::$config['system']['multithread']) ? 'true' : 'false';
        IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set multithread mode to \''.$str.'\'.');
    }

    /**
     * Handle color CLI Parameter.
     *
     * @param bool $value
     */
    public static function enableColor($value)
    {
        Synful::$config['system']['color'] = ($value == null) ? true : json_decode($value);
        $str = (Synful::$config['system']['color']) ? 'true' : 'false';
        IOFunctions::out(LogLevel::NOTE, 'CONFIG: Set console color to \''.$str.'\'.');
    }
}
