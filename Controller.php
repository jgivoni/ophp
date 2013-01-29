<?php

abstract class Controller implements ControllerInterface {
	
	const RESPONSE_HTML = 1;
	const RESPONSE_REDIRECT = 2;
	
	/**
	 * @var SqlDatabaseAdapterInterface
	 */
	private $dba;
	
	/**
	 * @var Server The server object that spawned the controller
	 */
	private $server;
	
	protected $dataMappers = array();

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
	 * @return HttpRequest
	 */
	protected function getRequest() {
		return $this->getServer()->getRequest();
	}

	
}
