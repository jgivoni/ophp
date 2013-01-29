<?php

/**
 * A controller takes a request and returns a response
 * 
 * The controller should minimize evalutation of the request url, since the router should do the mapping
 * That way it will be easier to issue an internal request to a controller, without going via the router
 */
interface ControllerInterface {
	public function __invoke();
}