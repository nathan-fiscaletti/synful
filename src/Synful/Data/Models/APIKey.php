<?php

namespace Synful\Data\Models;

use Synful\Synful;
use Synful\Framework\RateLimit;
use Illuminate\Database\Eloquent\Model;

class APIKey extends Model
{
    /**
     * The database connection to use for this model.
     *
     * @var string
     */
    protected $connection = 'synful';

    /**
     * The table to associate this model with.
     *
     * @var string
     */
    protected $table = 'api_keys';

    /**
     * The fillable properties for this Model.
     *
     * @var array
     */
    protected $fillable = [
        'api_key',
        'name',
        'auth',
        'whitelist_only',
        'enabled',
        'security_level',
        'allowed_request_handlers',
    ];

    /**
     * The properties to hide for this Model.
     *
     * @var
     */
    protected $hidden = [
        'id',
    ];

    /**
     * Create a new instance of the APIKey with data from the database.
     *
     * @param mixed $auth
     */
    public static function init($auth)
    {
        return self::where('auth', '=', $auth)->limit(1)->first();
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

        $this->api_key = $new_key['hash'];
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
        if (! password_verify($private_key, $this->api_key)) {
            return -1;
        }

        if (! ($this->security_level >= $security_level)) {
            return 0;
        }

        return 1;
    }

    /**
     * Retrieve all Firewall Entries for this APIKey.
     *
     * @return QueryBuilder
     */
    public function firewall()
    {
        return FirewallEntry::where('api_key_id', '=', $this->id);
    }

    /**
     * Checks if an IP is already firewalled for the key.
     *
     * @param  string $ip
     * @return bool
     */
    public function isFirewalled($ip)
    {
        return FirewallEntry::
            where([
                ['api_key_id', '=', $this->id],
                ['ip', '=', $ip],
            ])->limit(1)->first()
        != null;
    }

    /**
     * Add an IP to the APIKeys firewall.
     *
     * @param string  $ip
     * @param int     $block
     */
    public function firewallIP($ip, $block = 0)
    {
        $entry = FirewallEntry::
            where([
                ['api_key_id', '=', $this->id],
                ['ip', '=', $ip],
            ])->limit(1)->first();

        if ($entry != null) {
            $entry->block = $block;
            $entry->save();
        } else {
            $entry = new FirewallEntry();
            $entry->api_key_id = $this->id;
            $entry->ip = $ip;
            $entry->block = $block;
            $entry->save();
        }
    }

    /**
     * Removes an entry from the firewall.
     *
     * @param string $ip
     */
    public function unfirewallIP($ip)
    {
        $entry = FirewallEntry::
            where([
                ['api_key_id', '=', $this->id],
                ['ip', '=', $ip],
            ])->limit(1)->first();

        if ($entry != null) {
            $entry->delete();
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
        $entry = FirewallEntry::
            where([
                ['api_key_id', '=', $this->id],
                ['ip', '=', $ip],
            ])->limit(1)->first();

        return ($entry == null)
            ? false
            : $entry->blocked;
    }

    /**
     * Checks if an IP is white listed in the API Keys firewall.
     *
     * @param  string $ip
     * @return bool
     */
    public function isFirewallWhiteListed($ip)
    {
        return ! $this->isFirewallBlackListed($ip);
    }

    /**
     * Retrieve the API key's rate limit.
     *
     * @return \Synful\Framework\RateLimit
     */
    public function getRateLimit()
    {
        return new RateLimit(
            $this->auth,
            $this->rate_limit,
            $this->rate_limit_seconds
        );
    }

    /**
     * Add a new request handler to this API keys access array.
     *
     * @param string $endpoint
     * @return bool
     */
    public function addRequestHandler($endpoint)
    {
        $selected_request_handler = null;
        foreach (Synful::$request_handlers as $request_handler) {
            if ($request_handler->endpoint == $endpoint) {
                $selected_request_handler = $request_handler;
                break;
            }
        }

        if ($selected_request_handler == null && $endpoint != '*') {
            return false;
        }

        $associated_rh = $this->allowed_request_handlers;
        $current_rh = null;
        try {
            $current_rh = json_decode($associated_rh, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $current_rh = [];
        }

        if ($current_rh === null) {
            return false;
        }

        $current_rh[] = $endpoint;

        $this->allowed_request_handlers = json_encode($current_rh);

        return true;
    }

    /**
     * Retrieve the current request handlers  from this API keys access array.
     *
     * @return array
     */
    public function getRequestHandlersParsed()
    {
        $associated_rh = $this->allowed_request_handlers;
        $current_rh = null;
        try {
            $current_rh = json_decode($associated_rh, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $current_rh = [];
        }

        if ($current_rh === null) {
            return [];
        } else {
            return $current_rh;
        }
    }

    /**
     * Removes a request handler to this API keys access array.
     *
     * @param  string $endpoint
     * @return bool
     */
    public function removeRequestHandler($endpoint)
    {
        $associated_rh = $this->allowed_request_handlers;
        $current_rh = null;
        try {
            $current_rh = json_decode($associated_rh, true);
        } catch (\Exception $e) {
            $current_rh = [];
        }

        if ($current_rh == null || ! in_array($endpoint, $current_rh)) {
            return false;
        }

        unset($current_rh[array_search($endpoint, $current_rh)]);

        $this->allowed_request_handlers = json_encode($current_rh);

        return true;
    }

    /**
     * The Model's boot function.
     */
    public static function boot()
    {
        parent::boot();

        self::deleted(function (APIKey $apikey) {
            $apikey->firewall()->delete();
        });
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
        int    $rate_limit,
        int    $rate_limit_seconds,
        bool   $print_key = false,
        bool   $minimal = false
    ) {
        $ret = null;

        $key = self::getApiKey($auth);
        if ($key === null) {
            $new_key = self::generateNew();

            $unsaved = new self();

            $unsaved->api_key = $new_key['hash'];
            $unsaved->name = $name;
            $unsaved->auth = $auth;
            $unsaved->whitelist_only = $whitelist_only;
            $unsaved->enabled = 1;
            $unsaved->security_level = $security_level;
            $unsaved->rate_limit = $rate_limit;
            $unsaved->rate_limit_seconds = $rate_limit_seconds;
            $unsaved->allowed_request_handlers = json_encode([]);

            $unsaved->save();
            $ret = $unsaved;

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

        return $ret;
    }

    /**
     * Retreieves a key associated with the auth handle passed.
     *
     * @param  mixed $auth
     * @return APIKey
     */
    public static function getApiKey($auth)
    {
        return self::init($auth);
    }

    /**
     * Generates a new random hex string to use as API Key.
     *
     * @return array
     */
    public static function generateNew()
    {
        $key = bin2hex(openssl_random_pseudo_bytes(32));
        $hash = password_hash($key, PASSWORD_BCRYPT, ['cost' => sf_conf('security.api_key_cost')]);

        return [
            'key' => $key,
            'hash' => $hash,
        ];
    }
}
