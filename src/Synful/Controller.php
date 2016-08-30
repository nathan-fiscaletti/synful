<?php

	namespace Synful;

	use Synful\DataManagement\Models\ApiKey;
	use Synful\Response;
	use Synful\IO\IOFUnctions;
	use Synful\IO\LogLevel;
	use Synful\RequestHandlers\Interfaces\RequestHandler;
	
	use stdClass;
	use Exception;


	class Controller {

		/**
		 * Passes a JSON Request through the desired request handlers and returns a response
		 * Validates authentication and request integrity
		 * 
		 * @param  String   $request The request data to handle
		 * @param  Stripg   $ip      The ip address of the client making the request
		 * @return Response          The response object to be returned to the user
		 */
		public function handleRequest($request, $ip){

			$data = (array)json_decode($request);
			$response = new Response(null, $ip);

			if($this->validateRequest($data, $response) && $this->validateHandler($data, $response)){
				$handler =& Synful::$request_handlers[$data['handler']];
				$api_key = null;
				if($this->validateAuthentication($data, $response, $api_key, $handler, $ip)){
					$handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
				}
			}

			return $response;

		}

		/**
		 * Generates a master API Key if one does not already exist
		 * 
		 * @return APIKey The generated APIKey
		 */
		public function generateMasterKey(){
			if(!APIKey::isMasterSet()){
				IOFunctions::out(LogLevel::INFO, 'No master key found. Generating new master key.');
				$apik = APIKey::addNew(Synful::$config['security']['name'], Synful::$config['security']['email'], 0, 1, true);
				if($apik == NULL){
					IOFunctions::out(LogLevel::WARN, 'Failed to get master key.');
				}
				return $apik;
			}else{
				return APIKey::getMasterKey();
			}
		}

		/**
		 * Validates that the assigned handler is valid in the system
		 * 
		 * @param  Array    &$data     The request data
		 * @param  Response &$response The response data
		 * @return Boolean             True if the handler has been validated
		 */
		private function validateHandler(Array &$data, Response &$response){

			$response->request = $data['request'];

			$return = false;

			if(!empty($data['handler'])){
				if(file_exists('./src/Synful/RequestHandlers/' . $data['handler'] . '.php')){
					$return = true;
				}else{
					$response->code = 500;
					$response->setResponse('error', 'Unknown Handler: ' . $data['handler'] . '. Handlers are case sensitive.');
					$return = false;
				}
			}else{
				$response->code = 500;
				$response->setResponse('error', 'No handler defined.');
				$return = false;
			}

			return $return;
			
		}

		/**
		 * Validate a request with the system
		 * 
		 * @param  Array    &$data     The request data
		 * @param  Response &$response The response data
		 * @return Boolean             True if the request has been validated
		 */
		private function validateRequest(Array &$data, Response &$response){
			$return = false;

			if (!empty($data['request'])){
				if ($data['request'] instanceof stdClass){
					try{
						$data['request'] = (Array)$data['request'];
						if(is_array($data['request'])){
							$return = true;
						}else{
							$response->code = 400;
							$response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
							$return = false;
						}
					}catch(Exception $e){
						$response->code = 400;
						$response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
						$return = false;
					}
				}else{
					$response->code = 400;
					$response->setResponse('error', 'Bad request: Invalid request field supplied. Not Object.');
					$return = false;
				}
			}else{
				$response->code = 400;
				$response->setResponse('error', 'Bad request: Missing request field');
				$return = false;
			}

			return $return;
			
		}

		/**
		 * Validates the authentication of the request
		 * 
		 * @param  Array            &$data     The request data
		 * @param  Response         &$response The response data
		 * @param  Object           &$api_key  The $api_key variable by reference
		 * @param  RequestHandler   &$handler  The $handler by reference
		 * @param  String           &$ip       The clients IP Adress
		 * @return Boolean                     True if authentication has been validated
		 */
		private function validateAuthentication(Array &$data, Response &$response, &$api_key, RequestHandler &$handler, String &$ip){

			$return = true;

			if(!Synful::$config['security']['allow_public_requests'] || !(property_exists($handler, 'is_public') && $handler->is_public)){
				$return = false;
				if(!empty($data['user'])){
					if(!empty($data['key'])){
						if(APIKey::keyExists($data['user'])){
							$api_key = APIKey::getkey($data['user']);
							if($api_key->authenticate($data['key'])){
								return $this->validateFireWall($api_key, $response, $ip);
							}else{
								$response->code = 400;
								$response->setResponse('error', 'Bad Request: Invalid user or key');
								$return = false;
							}
						}else{
							$response->code = 400;
							$response->setResponse('error', 'Bad Request: Invalid user or key');
							$return = false;
						}
					}else{
						$response->code = 400;
						$response->setResponse('error', 'Bad Request: No key defined');
						$return = false;
					}
				}else{
					$response->code = 400;
					$response->setResponse('error', 'Bad Request: No user defined');
					$return = false;
				}
			}

			return $return;
		}

		/**
		 * Validate the firewall of an APIKey
		 * 
		 * @param  APIKey   &$api_key  The APIKey to use for validation
		 * @param  Response &$response The response data
		 * @param  String   $ip        The clients IP to test against the firewall entries
		 * @return Boolean             True if the firewall has been validated
		 */
		private function validateFireWall(APIKey &$api_key, Response &$response, String $ip){
			if($api_key->whitelist_only){
				if(!$api_key->isFirewallWhiteListed($ip)){
					$response->code = 500;
					$response->setResponse('error', 'Access Denied: Source IP is not whitelisted while on whitelist only key');
					return false;
				}
			}

			if($api_key->isFirewallBlackListed($ip)){
				$response->code = 500;
				$response->setResponse('error', 'Access Denied: Source IP Blacklisted');
				return false;
			}

			return true;
		}
	}
?>