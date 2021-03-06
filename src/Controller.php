<?php

namespace Ophp;

abstract class Controller implements ControllerInterface {
	
	/**
	 * @var Server The server object that spawned the controller
	 */
	private $server;
	
	public function __construct() {
	}
	
	public function setServer(Server $server) {
		$this->server = $server;
	}
	
	/**
	 *
	 * @return Server
	 */
	protected function getServer() {
		return $this->server;
	}

	/**
	 *
	 * @return requests\Request
	 */
	protected function getRequest() {
		return $this->getServer()->getRequest();
	}

	/**
	 * Return a new response object depending on the request type
	 * @return \Ophp\HttpResponse
	 */
	protected function newResponse() {
        return new Response;
	}
	
}
