<?php

namespace Synful\DataManagement\Models;

use Synful\Synful;
use Synful\DataManagement\Models\APIKeyPermissions;
use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;

/**
 * Class used for managing API Keys
 */
class APIKey
{

    /**
     * The database id of the key
     *
     * @var integer
     */
    public $id;

    /**
     * The Key Hash for the key
     *
     * @var string
     */
    public $key;

    /**
     * The name associated with the key
     *
     * @var string
     */
    public $name;

    /**
     * The email associated with the key
     *
     * @var stirng
     */
    public $email;

    /**
     * The whitelist_only value for the key
     *
     * @var boolean
     */
    public $whitelist_only;

    /**
     * The is_master value for the key
     *
     * @var boolean
     */
    public $is_master;

    /**
     * The enabled status for the key
     *
     * @var boolean
     */
    public $enabled;

    /**
     * The permissions object associated with the key
     *
     * @var APIKeyPermissions
     */
    public $permissions;

    /**
     * The firewall entries for the key
     *
     * @var array
     */
    public $ip_firewall = [];

    /**
     * Private array used for storing removed firewall entries before saving
     *
     * @var array
     */
    private $removed_ip_firewall = [];


    /**
     * Create a new instance of the APIKey with data from the database
     *
     * @param integer $id
     */
    public function __construct($id)
    {
        $result = mysqli_fetch_assoc(
            Synful::$sql->executeSql(
                'SELECT * FROM `api_keys` WHERE `id` = ?',
                [
                 's',
                 $id,
                ],
                true
            )
        );

        $this->id             = $result['id'];
        $this->key            = $result['api_key'];
        $this->name           = $result['name'];
        $this->email          = $result['email'];
        $this->whitelist_only = $result['whitelist_only'];
        $this->is_master      = $result['is_master'];
        $this->enabled        = $result['enabled'];

        $this->permissions = new APIKeyPermissions(
            $id,
            Synful::$config['default_permissions']['put_data'],
            Synful::$config['default_permissions']['get_data'],
            Synful::$config['default_permissions']['mod_data']
        );

        if ($this->is_master) {
            $this->permissions->put_data = 1;
            $this->permissions->get_data = 1;
            $this->permissions->mod_data = 1;
            $this->permissions->save();
        }

        $fw = Synful::$sql->executeSql(
            'SELECT * FROM `ip_firewall` WHERE `api_key_id` = ?',
            [
             's',
             $id,
            ],
            true
        );

        while ($ip_list = mysqli_fetch_assoc($fw)) {
            $this->ip_firewall[$ip_list['ip']]
            = [
             'ip'    => $ip_list['ip'],
             'block' => $ip_list['block'],
            ];
        }
    }


    /**
     * Checks if an IP is already firewalled for the key
     *
     * @param  string $ip
     * @return boolean
     */
    public function isFirewalled($ip)
    {
        return isset($this->ip_firewall[$ip]);
    }


    /**
     * Add an IP to the APIKeys firewall
     *
     * @param string  $ip
     * @param integer $block
     */
    public function firewallIP($ip, $block = 0)
    {
        $this->ip_firewall[$ip]
        = [
         'ip'    => $ip,
         'block' => $block,
        ];
    }


    /**
     * Removes an entry from the firewall
     *
     * @param string $ip
     */
    public function unfirewallIP($ip)
    {
        for ($i = 0; $i < count($this->ip_firewall); $i++) {
            if ($this->ip_firewall[$i]['ip'] == $ip) {
                array_push(
                    $this->removed_ip_firewall,
                    $this->ip_firewall[$i]
                );
                unset($this->ip_firewall[$i]);
                break;
            }
        }
    }


    /**
     * Checks if an IP is black listed in the API Keys firewall
     *
     * @param  string $ip
     * @return boolean
     */
    public function isFirewallBlackListed($ip)
    {
        $ret = false;

        foreach ($this->ip_firewall as $firewall_entry) {
            if ($firewall_entry['ip'] == $ip) {
                $ret = $firewall_entry['block'];
                break;
            }
        }

        return $ret;
    }


    /**
     * Checks if an IP is white listed in the API Keys firewall
     *
     * @param  string $ip
     * @return boolean
     */
    public function isFirewallWhiteListed($ip)
    {
        return !$this->isFirewallBlackListed($ip);
    }


    /**
     * Saves the APIKey to the database and updates it's firewall and perms
     */
    public function save()
    {
        // Update the API Keys Entry
        Synful::$sql->executeSql(
            'UPDATE `api_keys` SET `api_key` = ?, `name` = ?, '.
            '`email` = ?, `whitelist_only` = ?, `is_master` = ?, '.
            '`enabled` = ? WHERE `id` = ?',
            [
             'sssssss',
             $this->key,
             $this->name,
             $this->email,
             $this->whitelist_only,
             $this->is_master,
             $this->enabled,
             $this->id,
            ]
        );

        // Remove selected IP Firewall Entries
        foreach ($this->removed_ip_firewall as $removed_firewall_entry) {
            Synful::$sql->executeSql(
                'DELETE FROM `ip_firewall` WHERE `ip` = ? '.
                'AND `api_key_id` = ?',
                [
                 'si',
                 $removed_firewall_entry['ip'],
                 (int) $this->id,
                ]
            );
        }

        // Update IP Firewalls
        foreach ($this->ip_firewall as $firewall_entry) {
            $res = Synful::$sql->executeSql(
                'SELECT `ip` FROM `ip_firewall` WHERE `api_key_id` = ? '.
                'AND `ip` = ?',
                [
                 'is',
                 (int) $this->id,
                 $firewall_entry['ip'],
                ],
                true
            );

            if ($res->num_rows < 1) {
                Synful::$sql->executeSql(
                    'INSERT INTO `ip_firewall` (api_key_id, ip, block) '.
                    'VALUES (?, ?, ?)',
                    [
                     'isi',
                     (int) $this->id,
                     $firewall_entry['ip'],
                     (int) $firewall_entry['block'],
                    ]
                );
            }
        }//end foreach

        // Update Permissions
        $this->permissions->save();
    }


    /**
     * Deletes the entry from the database
     */
    public function delete()
    {
        Synful::$sql->executeSql(
            'DELETE FROM `api_keys` WHERE `id` = ?',
            [
             's',
             $this->id,
            ]
        );

        Synful::$sql->executeSql(
            'DELETE FROM `ip_firewall` WHERE `api_key_id` = ?',
            [
             's',
             $this->id,
            ]
        );

        $this->permissions->delete();
    }


    /**
     * Try to authenticate with a private key
     *
     * @param  string $private_key
     * @return boolean
     */
    public function authenticate($private_key)
    {
        return password_verify($private_key, $this->key);
    }


    /**
     * Adds a new APIKey to the database
     *
     * @param  string  $name
     * @param  string  $email
     * @param  integer $whitelist_only
     * @param  integer $is_master
     * @param  boolean $print_key
     * @return APIKey
     */
    public static function addNew($name, $email, $whitelist_only, $is_master = 0, $print_key = false)
    {
        $ret = null;

        if (!self::keyExists($email)) {
            $new_key = self::generateNew();

            Synful::$sql->executeSql(
                'INSERT INTO `api_keys` (`api_key`, `name`, `email`, '.
                '`whitelist_only`, `is_master`, `enabled`) VALUES '.
                '(?, ?, ?, ?, ?, ?)',
                [
                 'ssssss',
                 $new_key['hash'],
                 $name,
                 $email,
                 $whitelist_only,
                 $is_master,
                 1,
                ]
            );

            if ($print_key) {
                IOFunctions::out(
                    LogLevel::INFO,
                    'New Private '.(($is_master) ? 'Master' : '').
                    ' API Key: '.$new_key['key'],
                    true,
                    false,
                    false
                );
            }

            $ret = self::getKey($email);
        }//end if

        return $ret;
    }


    /**
     * Retreieves a key associated with the ID passed
     *
     * @param  mixed $id
     * @return APIKey
     */
    public static function getKey($id)
    {
        $keys = Synful::$sql->executeSql(
            'SELECT `id` FROM `api_keys` WHERE `id` = ? OR `email` = ?',
            [
             'ss',
             $id,
             $id,
            ],
            true
        );

        $ret = null;

        if (mysqli_num_rows($keys) > 0) {
            $ret = new APIKey(mysqli_fetch_assoc($keys)['id']);
        }

        return $ret;
    }


    /**
     * Check if a key exists in the system
     *
     * @param  mixed $id
     * @return boolean
     */
    public static function keyExists($id)
    {
        return (self::getKey($id) != null);
    }


    /**
     * Returns the master key if one exists in the system
     *
     * @return APIKey
     */
    public static function getMasterKey()
    {
        $result = Synful::$sql->executeSql(
            'SELECT * FROM `api_keys` where `is_master` = 1',
            [],
            true
        );
        $ret    = null;

        if (mysqli_num_rows($result) > 0) {
            $ret = new APIKey(mysqli_fetch_assoc($result)['id']);
        }

        return $ret;
    }


    /**
     * Checks if the master key has already been set
     *
     * @return boolean
     */
    public static function isMasterSet()
    {
        return (self::getMasterKey() != null);
    }


    /**
     * Generates a new random hex string to use as API Key
     *
     * @return array
     */
    public static function generateNew()
    {
        $key  = bin2hex(openssl_random_pseudo_bytes(32));
        $hash = password_hash($key, PASSWORD_BCRYPT, ['cost' => 11]);
        return [
                'key'  => $key,
                'hash' => $hash,
               ];
    }
}
