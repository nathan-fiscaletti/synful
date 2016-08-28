<?php

	namespace Synful\Standalone;

	use Synful\Synful;
	use Synful\IO\IOFunctions;
	use Synful\IO\LogLevel;

	class Standalone {

		public function initialize(){
			$this->run();
		}

		private function run(){
			set_time_limit (0);

			$sock = socket_create(AF_INET, SOCK_STREAM, 0);
			$bind = socket_bind($sock, Synful::$config['system']['ip'], Synful::$config['system']['port']);

			if($bind){
				IOFunctions::out(LogLevel::INFO, 'Listening on ' . Synful::$config['system']['ip'] . ':' . Synful::$config['system']['port']);
			}else{
				exit(1);
			}
			
			socket_listen($sock);

			while(true){
				$client = socket_accept($sock);
				$client_ip = '';
				$client_port = 0;
				socket_getpeername($client, $client_ip, $client_port);
				if(Synful::$config['system']['multithread']){
					(new \Synful\Standalone\ClientHandleMultiThread($client, $client_ip, $client_port))->start();
				}else{
					(new \Synful\Standalone\ClientHandle($client, $client_ip, $client_port))->start();
				}
			}
		}


	}
?>