<?php
	class Response implements JsonSerializable {

		public $request;
		public $code;
		public $response;

		/**
		 * Create the Response object using the supplied request data
		 * @param String $request_data The request data
		 */
		public function __construct($request_data = null){
			$this->request = $request_data;
		}

		/**
		 * Overrides full response object with custom array of data
		 * @param  Array $data The data to override the full response with
		 */
		public function overloadResponse(Array $data){
			$this->response = $data;
		}

		/**
		 * Add a list of responses to the response object
		 * @param Array $responses The responses to add
		 */
		public function addResponses(Array $responses){
			$this->response = array_merge($this->response, $responses);
		}

		/**
		 * Adds data to the data variable that will be returned with the object
		 * @param String $key  The key to put the data in
		 * @param Object $data The data to set
		 */
		public function setResponse($key, $data){
			$this->response[$key] = $data;
		}

		/**
		 * Override serialization for json_encode to ommit $request variable
		 * @return Array 
		 */
		public function jsonSerialize(){
			return [ 'code' => $this->code, 'response' => $this->response ];
		}

	}
?>