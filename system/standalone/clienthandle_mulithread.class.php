<?php
	
	include './system/controller.class.php';

	class ClientHandle_MultiThread extends Thread {

		private $client_socket;
		private $ip;
		private $port;

		public function __construct($client_socket, $ip, $port){
			$this->client_socket = $client_socket;
			$this->ip = $ip;
			$this->port = $port;
		}

		/**
		 * Initializes the new handler for the request
		 */
		public function run(){
				$input = socket_read($this->client_socket, 2024);

				IOFunctions::out(LogLevel::INFO, 'Client REQ (' . $this->ip . ':' . $this->port . '): ' . $input);

				$response = Synful::$controller->handleRequest($input, $this->ip);

				socket_write($this->client_socket, json_encode($response), strlen(json_encode($response)));	

				IOFunctions::out(LogLevel::INFO, 'Server RES (' . $this->ip . ':' . $this->port . '): ' . json_encode($response));

			    socket_close($this->client_socket);

			    unset($this);
		}
	}
?>