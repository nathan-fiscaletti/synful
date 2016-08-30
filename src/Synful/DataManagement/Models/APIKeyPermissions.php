<?php

	namespace Synful\DataManagement\Models;

	use Synful\Synful;

	class APIKeyPermissions {
		public $api_key_id = -1;
		public $put_data = 0;
		public $get_data = 0;
		public $mod_data = 0;

		public function __construct($id, $put_data = 0, $get_data = 0, $mod_data = 0){
			$this->api_key_id = $id;

			$res = Synful::$sql->executeSql('SELECT * FROM `api_perms` WHERE `api_key_id` = ?', true, ['s', $this->api_key_id]);
			if(mysqli_num_rows($res) > 0){
				$res = mysqli_fetch_assoc($res);
				$this->put_data = $res['put_data'];
				$this->get_data = $res['get_data'];
				$this->mod_data = $res['mod_data'];
			}else{
				$this->put_data = $put_data;
				$this->get_data = $get_data;
				$this->mod_data = $mod_data;
				$this->save();
			}

		}

		public function save(){
			Synful::$sql->executeSql('INSERT INTO `api_perms` (`api_key_id`, `put_data`, `get_data`, `mod_data`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `put_data` = ?, `get_data` = ?, `mod_data` = ?', false, ['iiiiiii', (int)$this->api_key_id, (int)$this->put_data, (int)$this->get_data, (int)$this->mod_data, (int)$this->put_data, (int)$this->get_data, (int)$this->mod_data]);
		}

		public function delete(){
			Synful::$sql->executeSql('DELETE FROM `api_perms` WHERE `api_key_id` = ?', false, ['s', $this->api_key_id]);
		}
	}
	
?>