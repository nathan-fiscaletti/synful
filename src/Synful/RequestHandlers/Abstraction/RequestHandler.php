<?php

	namespace Synful\RequestHandlers\Abstraction;

	use Synful\Response;

	abstract class RequestHandler {

		public $is_public = false;

		/**
		 * Function for handling request and returning data as a Response object
		 * @param  Response $data              The data received by reference
		 * @param  boolean  $is_master_request True if the key being used to access the request is a master key
		 */
		public abstract function handleRequest(Response &$data, $is_master_request = false);

	}

?>