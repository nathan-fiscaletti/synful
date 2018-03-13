<?php

namespace Synful\Util\DataManagement\Models;

use Synful\Util\Framework\SynfulException;

/**
 * Class used for managing API Keys.
 */
class APIKey
{
    /**
     * The database id of the key.
     *
     * @var int
     */
    public $id;

    /**
     * The Key Hash for the key.
     *
     * @var string
     */
    public $key;

    /**
     * The name associated with the key.
     *
     * @var string
     */
    public $name;

    /**
     * The auth handle associated with the key.
     *
     * @var stirng
     */
    public $auth;

    /**
     * The whitelist_only value for the key.
     *
     * @var bool
     */
    public $whitelist_only;

    /**
     * The enabled status for the key.
     *
     * @var bool
     */
    public $enabled;

    /**
     * The security level for the key.
     *
     * @var int
     */
    public $security_level;

    /**
     * The firewall entries for the key.
     *
     * @var array
     */
    public $ip_firewall = [];

    /**
     * Private array used for storing removed firewall entries before saving.
     *
     * @var array
     */
    private $removed_ip_firewall = [];

    /**
     * Create a new instance of the APIKey with data from the database.
     *
     * @param mixed $auth_or_id
     */
    public function __construct($auth_or_id)
    {
        $sql_result = sf_sql(
            'SELECT * FROM `api_keys` WHERE `id` = ? OR `auth` = ?',
            [
             'ss',
             $auth_or_id,
             $auth_or_id,
            ],
            true
        );

        if (mysqli_num_rows($sql_result) < 1) {
            throw new SynfulException(-100, -100, 'Not Found.');
        }

        $result = mysqli_fetch_assoc($sql_result);

        $this->id = $result['id'];
        $this->key = $result['api_key'];
        $this->name = $result['name'];
        $this->auth = $result['auth'];
        $this->whitelist_only = $result['whitelist_only'];
        $this->enabled = $result['enabled'];
        $this->security_level = $result['security_level'];

        $fw = sf_sql(
            'SELECT * FROM `ip_firewall` WHERE `api_key_id` = ?',
            [
             's',
             $this->id,
            ],
            true
        );

        while ($ip_list = mysqli_fetch_assoc($fw)) {
            $this->ip_firewall[$ip_list['ip']] =
            [
             'ip'   => $ip_list['ip'],
             'block' => $ip_list['block'],
            ];
        }
    }

    /**
     * Checks if an IP is already firewalled for the key.
     *
     * @param  string $ip
     * @return bool
     */
    public function isFirewalled($ip)
    {
        return isset($this->ip_firewall[$ip]);
    }

    /**
     * Add an IP to the APIKeys firewall.
     *
     * @param string  $ip
     * @param int $block
     */
    public function firewallIP($ip, $block = 0)
    {
        $this->ip_firewall[$ip] =
        [
         'ip'   => $ip,
         'block' => $block,
        ];
    }

    /**
     * Removes an entry from the firewall.
     *
     * @param string $ip
     */
    public function unfirewallIP($ip)
    {
        foreach ($this->ip_firewall as $firewall_entry) {
            if ($firewall_entry['ip'] == $ip) {
                array_push(
                    $this->removed_ip_firewall,
                    $firewall_entry
                );
                unset($this->ip_firewall[$firewall_entry['ip']]);
                break;
            }
        }
    }

    /**
     * Checks if an IP is black listed in the API Keys firewall.
     *
     * @param  string $ip
     * @return bool
     */
    public function isFirewallBlackListed($ip)
    {
        $ret = false;

        foreach ($this->ip_firewall as $firewall_entry) {
            if ($firewall_entry['ip'] == $ip) {
                $ret = $firewall_entry['block'] === 1;
                break;
            }
        }

        return $ret;
    }

    /**
     * Checks if an IP is white listed in the API Keys firewall.
     *
     * @param  string $ip
     * @return bool
     */
    public function isFirewallWhiteListed($ip)
    {
        $ret = false;

        foreach ($this->ip_firewall as $firewall_entry) {
            if ($firewall_entry['ip'] == $ip) {
                $ret = $firewall_entry['block'] === 0;
                break;
            }
        }

        return $ret;
    }

    /**
     * Saves the APIKey to the database and updates it's firewall and perms.
     */
    public function save()
    {
        // Update the API Keys Entry
        sf_sql(
            'UPDATE `api_keys` SET `api_key` = ?, `name` = ?, '.
            '`auth` = ?, `whitelist_only` = ?, '.
            '`enabled` = ?, `security_level` = ? WHERE `id` = ?',
            [
             'sssssss',
             $this->key,
             $this->name,
             $this->auth,
             $this->whitelist_only,
             (int) $this->enabled,
             (int) $this->security_level,
             $this->id,
            ]
        );

        // Remove selected IP Firewall Entries
        foreach ($this->removed_ip_firewall as $removed_firewall_entry) {
            sf_sql(
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
            $res = sf_sql(
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
                sf_sql(
                    'INSERT INTO `ip_firewall` (api_key_id, ip, block) '.
                    'VALUES (?, ?, ?)',
                    [
                     'isi',
                     (int) $this->id,
                     $firewall_entry['ip'],
                     (int) $firewall_entry['block'],
                    ]
                );
            } else {
                sf_sql(
                    'UPDATE `ip_firewall` SET `block`=? WHERE '.
                    '`api_key_id`=? AND `ip`=?',
                    [
                     'iis',
                     (int) $firewall_entry['block'],
                     (int) $this->id,
                     $firewall_entry['ip'],
                    ]
                );
            }
        }
    }

    /**
     * Deletes the entry from the database.
     */
    public function delete()
    {
        sf_sql(
            'DELETE FROM `api_keys` WHERE `id` = ?',
            [
             's',
             $this->id,
            ]
        );

        sf_sql(
            'DELETE FROM `ip_firewall` WHERE `api_key_id` = ?',
            [
             's',
             $this->id,
            ]
        );
    }

    /**
     * Regenerate the key for this API Key.
     *
     * @param bool $print_key
     * @param bool $minimal
     */
    public function regen(
        $print_key = false,
        $minimal = false
    ) {
        $new_key = self::generateNew();

        $this->key = $new_key['hash'];
        $this->save();

        if ($print_key) {
            if (! $minimal) {
                sf_info(
                    'New Private API Key: '.$new_key['key']
                );
            } else {
                sf_info($new_key['key'], true, true);
            }
        }
    }

    /**
     * Try to authenticate with a private key.
     *
     * @param  string $private_key
     * @param  int    $security_level
     * @return int
     */
    public function authenticate($private_key, $security_level)
    {
        if (! password_verify($private_key, $this->key)) {
            return -1;
        }

        if (! ($this->security_level >= $security_level)) {
            return 0;
        }

        return 1;
    }

    /**
     * Adds a new APIKey to the database.
     *
     * @param  string  $name
     * @param  string  $auth
     * @param  int $whitelist_only
     * @param  int $security_level
     * @param  bool $print_key
     * @param  bool $minimal
     * @return APIKey
     */
    public static function addNew(
        string $name,
        string $auth,
        int    $whitelist_only,
        int    $security_level,
        bool   $print_key = false,
        bool   $minimal = false
    ) {
        $ret = null;

        $key = self::getKey($auth);
        if ($key === null) {
            $new_key = self::generateNew();

            sf_sql(
                'INSERT INTO `api_keys` (`api_key`, `name`, `auth`, '.
                '`whitelist_only`, `enabled`, `security_level`) VALUES '.
                '(?, ?, ?, ?, ?, ?)',
                [
                 'ssssss',
                 $new_key['hash'],
                 $name,
                 $auth,
                 $whitelist_only,
                 1,
                 $security_level,
                ]
            );

            if ($print_key) {
                if (! $minimal) {
                    sf_info(
                        'New Private API Key: '.$new_key['key']
                    );
                } else {
                    sf_info($new_key['key'], true, true);
                }
            }

            $ret = self::getKey($auth);
        }

        return $ret;
    }

    /**
     * Retreieves a key associated with the ID passed.
     *
     * @param  mixed $id
     * @return APIKey
     */
    public static function getKey($id)
    {
        $ret = null;

        try {
            $ret = new self($id);
        } catch (SynfulException $e) {
            $ret = null;
        }

        return $ret;
    }

    /**
     * Generates a new random hex string to use as API Key.
     *
     * @return array
     */
    public static function generateNew()
    {
        $key = bin2hex(openssl_random_pseudo_bytes(32));
        $hash = password_hash($key, PASSWORD_BCRYPT, ['cost' => 11]);

        return [
            'key' => $key,
            'hash' => $hash,
        ];
    }
}
