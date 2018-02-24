<?php

namespace Ophp;

/**
 * Encapsulates a generic http response
 */
class Response {
    const STATUS_ERROR = 'ERROR';
     
    protected $status;
    
    public $body = '';
    
    public function status($status) {
		$this->status = $status;
		return $this;
	}
    
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
    
    public function __toString() {
		return (string) $this->body;
	}
}