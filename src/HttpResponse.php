<?php

namespace Ophp;

/**
 * Encapsulates a generic http response
 */
class HttpResponse extends Response {
	const STATUS_OK = '200 OK';
	const STATUS_NOT_FOUND = '404 Not Found';
	const STATUS_INTERNAL_SERVER_ERROR = '500 Internal Server Error';
	
	public $type = 'undefined';
	public $headers = array();
	
	public function header($key, $value) {
		if (isset($key)) {
			$this->headers[$key] = $value;
		} else {
			$this->headers[] = $value;
		}
		return $this;
	}
	
	public function status($status) {
        parent::status($status);
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
	
}