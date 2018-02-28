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
		if ($body instanceof View) {
			$exception = $body->getToStringException();
			if (isset($exception)) {
				throw new \Exception('Failed to render view', 0, $exception);
			}
		}
		return $this;
	}
    
    public function __toString() {
		return (string) $this->body;
	}
}