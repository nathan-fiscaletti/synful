<?php

	namespace Synful;

	use Synful\DataManagement\Models\ApiKey;
	use Synful\Response;
	use Synful\IO\IOFUnctions;
	use Synful\IO\LogLevel;
	
	use stdClass;
	use Exception;


	class Controller {

		/**
		 * Generates a master API Key if one does not already exist
		 * 
		 * @return APIKey The generated APIKey
		 */
		public function generateMasterKey(){
			if(!APIKey::isMasterSet()){
				IOFunctions::out(LogLevel::INFO, 'No master key found. Generating new master key.');
				$apik = (APIKey::addNew(Synful::$config['security']['name'], Synful::$config['security']['email'], 0, 1));
				if($apik == NULL){
					IOFunctions::out(LogLevel::WARN, 'Failed to get master key.');
				}
				IOFunctions::out(LogLevel::INFO, 'New master key generated: ' . $apik->key);
				return $apik;
			}else{
				return APIKey::getMasterKey();
			}
		}

		/**
		 * Passes a JSON Request through the desired request handlers and returns a response
		 * Checks for authentication and request integrity
		 * 
		 * @param  String   $request The request data to handle
		 * @param  Stripg   $ip      The ip address of the client making the request
		 * @return Response          The response object to be returned to the user
		 */
		public function handleRequest($request, $ip){

			$data = (array)json_decode($request);
			$response = new Response();

			if(empty($data['request'])){
				$response->code = 400;
				$response->setResponse('error', 'Bad request: Missing request field');
				return $response;
			}

			if(!( $data['request'] instanceof stdClass )){
				$response->code = 400;
				$response->setResponse('error', 'Bad request: Invalid request field supplied. Not Object.');
				return $response;
			}

			try{
				$data['request'] = (Array)$data['request'];
			}catch(Exception $e){
				$response->code = 400;
				$response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
				return $response;	
			}

			if(!is_array($data['request'])){
				$response->code = 400;
				$response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
				return $response;	
			}

			$response->request = $data['request'];

			$api_key = null;

			if(!Synful::$config['security']['is_api_public']){
				if(empty($data['key'])){
					$response->code = 400;
					$response->setResponse('error', 'Bad Request: No key defined');
					return $response;
				}

				if(!APIKey::keyExists($data['key'])){
					$response->code = 400;
					$response->setResponse('error', 'Bad Request: Invalid key');
					return $response;
				}

				$api_key = APIKey::getkey($data['key']);

				if($api_key->whitelist_only){
					if(!$api_key->isFirewallWhiteListed($ip)){
						$response->code = 500;
						$response->setResponse('error', 'Access Denied: Source IP is not whitelisted while on whitelist only key');
						return $response;
					}
				}

				if($api_key->isFirewallBlackListed($ip)){
					$response->code = 500;
					$response->setResponse('error', 'Access Denied: Source IP Blacklisted');
					return $response;
				}
			}

			if(empty($data['handler'])){
				$response->code = 500;
				$response->setResponse('error', 'No handler defined.');
				return $response;
			}

			if(!file_exists('./src/Synful/RequestHandlers/' . $data['handler'] . '.php')){
				$response->code = 500;
				$response->setResponse('error', 'Unknown Handler: ' . $data['handler'] . '. Handlers are case sensitive.');
				return $response;
			}

			$handler = Synful::$request_handlers[$data['handler']];
			$handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);

			return $response;

		}
	}
?>