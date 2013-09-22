<?php

namespace Ophp;

/**
 * Encapsulates a generic http response
 */
class HttpResponse {
	const STATUS_OK = '200 OK';
	const STATUS_NOT_FOUND = '404 Not Found';
	const STATUS_INTERNAL_SERVER_ERROR = '500 Internal Server Error';
	
	public $type = 'undefined';
	public $headers = array();
	public $body = '';
	
	/**
	 * Sets the body of the request to the string $body
	 * 
	 * Chainable
	 * 
	 * @param string $body 
	 * @return HttpResponse This object
	 */
	public function body($body) {
		$this->body = (string) $body;
		return $this;
	}
	
	public function header($key, $value) {
		if (isset($key)) {
			$this->headers[$key] = $value;
		} else {
			$this->headers[] = $value;
		}
	}
	
	public function status($status) {
		$this->header(null, 'HTTP/1.1 '.$status);
		return $this;
	}
	
	/**
	 * Sets a header on the response that indicates request redirection
	 * 
	 * NB: This method does not stop script execution
	 * 
	 * @return HttpResponse This object for chaining
	 * @todo Control response status header (301/302)
	 */
	public function redirect($url) {
		$this->headers['Location'] = $url;
		return $this;
	}
	
	public function __toString() {
		return (string) $this->body;
	}
}