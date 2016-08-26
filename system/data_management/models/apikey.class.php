<?php

    include './system/data_management/models/apikey_permissions.class.php';

    class APIKey {
        public $id;
        public $key;
        public $name;
        public $email;
        public $whitelist_only;
        public $is_master;
        public $permissions;

        public $ip_firewall = [];
        private $removed_ip_firewall = [];

        public function __construct($id){
            $result = mysqli_fetch_assoc(Synful::$sql->executeSql('SELECT * FROM `api_keys` WHERE `id` = ?', true, ['s', $id]));
            $this->id = $result['id'];
            $this->key = $result['api_key'];
            $this->name = $result['name'];
            $this->email = $result['email'];
            $this->whitelist_only = $result['whitelist_only'];
            $this->is_master = $result['is_master'];
            $this->permissions = new APIKeyPermissions($id, Synful::$config['default_permissions']['put_data'], Synful::$config['default_permissions']['get_data'], Synful::$config['default_permissions']['mod_data']);

            if($this->is_master){
                $this->permissions->put_data = 1;
                $this->permissions->get_data = 1;
                $this->permissions->mod_data = 1;
                $this->permissions->save();
            }

            $firewall = Synful::$sql->executeSql('SELECT * FROM `ip_firewall` WHERE `api_key_id` = ?', true, ['s', $id]);
            while($ip_list = mysqli_fetch_assoc($firewall)){
                $this->ip_firewall[] = ['ip' => $ip_list['ip'], 'block' => $ip_list['block']];
            }
        }

        /**
         * Add an IP to the APIKeys firewall
         * @param  string  $ip    The IP Address to add
         * @param  integer $block Set to 1 if you want to blacklist this ip, else it will be whitelisted
         */
        public function firewallIP($ip, $block = 0){
            array_push($this->ip_firewall, ['ip' => $ip, 'block' => $block]);
        }

        /**
         * Removes an entry from the firewall
         * @param  String $ip The IP to remove from the firewall
         */
        public function unfirewallIP($ip){
            for($i=0;$i<count($this->ip_firewall);$i++){
                if($this->ip_firewall[$i]['ip'] == $ip){
                    array_push($this->removed_ip_firewall, $this->ip_firewall[$i]);
                    unset($this->ip_firewall[$i]);
                    return;
                }
            }
        }

        /**
         * Checks if an IP is black listed in the API Keys firewall
         * @param  String  $ip The IP to check for
         * @return boolean     True if the ip is black listed, false otherwise
         */
        public function isFirewallBlackListed($ip){
            foreach($this->ip_firewall as $firewall_entry){
                if($firewall_entry['ip'] == $ip){
                    return $firewall_entry['block'];
                }
            }

            return false;
        }

        /**
         * Checks if an IP is white listed in the API Keys firewall
         * @param  String  $ip The IP to check for
         * @return boolean     True if the ip is white listed, false otherwise
         */
        public function isFirewallWhiteListed($ip){
            return !$this->isFirewallBlackListed($ip);
        }

        /**
         * Saves the APIKey to the database and updates it's firewall and permissions
         */
        public function save(){

            // Update the API Keys Entry
            Synful::$sql->executeSql('UPDATE `api_keys` SET `api_key` = ?, `name` = ?, `email` = ?, `whitelist_only` = ?, `is_master` = ? WHERE `id` = ?', false, ['ssssss', $this->key, $this->name, $this->email, $this->whitelist_only, $this->is_master, $this->id]);

            // Remove selected IP Firewall Entries
            foreach($this->removed_ip_firewall as $removed_firewall_entry){
                Synful::$sql->executeSql('DELETE FROM `ip_firewall` WHERE `ip` = ? AND `api_key_id` = ?', false, ['si', $removed_firewall_entry['ip'], (int)$this->id]);
            }

            // Update IP Firewalls
            foreach($this->ip_firewall as $firewall_entry){
                $res = Synful::$sql->executeSql('SELECT `ip` FROM `ip_firewall` WHERE `api_key_id` = ? AND `ip` = ?', true, ['is', (int)$this->id, $firewall_entry['ip']]);
                if($res->num_rows < 1){
                    Synful::$sql->executeSql('INSERT INTO `ip_firewall` (api_key_id, ip, block) VALUES (?, ?, ?)', false, ['isi', (int)$this->id, $firewall_entry['ip'], (int)$firewall_entry['block']]);
                }
            }

            // Update Permissions
            $this->permissions->save();
        }

        /**
         * Adds a new APIKey to the database
         * @param  String $name            The name of the person to associate the APIKey with
         * @param  String $email           The email address to associate with the APIKey
         * @param  Int    $whitelist_only  1 to only allow IP's that have been whitelisted with the APIKey to access it
         * @param  Int    $is_master       1 if the key is to be a master key
         * @return APIKey                  The APIKey object generated
         */
        public static function addNew($name, $email, $whitelist_only, $is_master = 0){

            if(APIKey::keyExists($email)) return NULL;

            $new_key = APIKey::generateNew();
            Synful::$sql->executeSql('INSERT INTO `api_keys` (`api_key`, `name`, `email`, `whitelist_only`, `is_master`) VALUES (?, ?, ?, ?, ?)', false, 
            ['sssss', $new_key, $name, $email, $whitelist_only, $is_master]);
            return APIKey::getKey($new_key);

        }

        /**
         * Retreieves a key associated with the ID passed
         * @param  string|int $id The database id of the key, the API Key for the key or the email of the key
         * @return APIKey          An APIKey object
         */
        public static function getKey($id){
            $keys = Synful::$sql->executeSql('SELECT `id` FROM `api_keys` WHERE `id` = ? OR `api_key` = ? OR `email` = ?', true, ['sss', $id, $id, $id]);
            if(mysqli_num_rows($keys) > 0){
                return new APIKey(mysqli_fetch_assoc($keys)['id']);
            }else{
                return NULL;
            }
        }

        /**
         * Check if a key exists in the system
         * @param  string|int $id The database id of the key or the API Key for the key
         * @return boolean        True if key is found, otherwise false
         */
        public static function keyExists($id){
            return (APIKey::getKey($id) != NULL);
        }

        /**
         * Returns the master key if one exists in the system
         * @return APIKey  The master key. If none are found in the system, will return NULL
         */
        public static function getMasterKey(){
            $result = Synful::$sql->executeSql('SELECT * FROM `api_keys` where `is_master` = 1', true);
            if(mysqli_num_rows($result) > 0){
                return new APIKey(mysqli_fetch_assoc($result)['id']);
            }else{
                return NULL;
            }
        }

        /**
         * Checks if the master key has already been set
         * @return boolean true if the master key has already been set, otherwise false
         */
        public static function isMasterSet(){
            return (APIKey::getMasterKey() != NULL);
        }

        /**
         * Generates a new random hex string to use as API Key
         * @return string the new API key
         */
        public static function generateNew(){
            $ret = bin2hex(openssl_random_pseudo_bytes(32));;

            while(APIKey::keyExists($ret))
                $ret = bin2hex(openssl_random_pseudo_bytes(32));;

            return $ret;
        }
    }

?>