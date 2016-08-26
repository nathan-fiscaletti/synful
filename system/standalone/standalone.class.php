<?php
	
	if(Synful::$config['system']['multithread']){
		include './system/standalone/clienthandle_multithread.class.php';
	}else{
		include './system/standalone/clienthandle.class.php';
	}


	class Standalone {

		public function initialize(){
			$this->run();
		}

		private function run(){
			set_time_limit (0);

			$sock = socket_create(AF_INET, SOCK_STREAM, 0);
			$bind = socket_bind($sock, Synful::$config['system']['ip'], Synful::$config['system']['port']);

			IOFunctions::out(LogLevel::INFO, 'Listening on ' . Synful::$config['system']['ip'] . ':' . Synful::$config['system']['port']);
			
			socket_listen($sock);

			while(true){
				$client = socket_accept($sock);
				$client_ip = '';
				$client_port = 0;
				socket_getpeername($client, $client_ip, $client_port);
				if(Synful::$config['system']['multithread']){
					(new ClientHandle_MultiThread($client, $client_ip, $client_port))->start();
				}else{
					(new ClientHandle($client, $client_ip, $client_port))->start();
				}
			}
		}


	}
?>