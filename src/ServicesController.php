<?php

namespace Ophp;

abstract class ServicesController implements ControllerInterface {
	
	function __construct() {
		$this->setDbService('localhost', 'replanner', 'webapp', 'BFvmny5awwFvbvRt');
	}
	// Shutdown event notification
	function shutdown() {
		// Shutdown dependencies
		// Stop services
		foreach ($this->services as $service) {
			if ($service->isStarted()) {
				$service->stopService();
			}
		}
		// Do shutdown of controller itself
	}
	
	// Deferred Dependency Injection
	
	/**
	 *
	 * @var array ServiceContainer list, indexed by service name
	 */
	protected $services = array();
	
	function __get($key) {
		if ($this->isService($key)) {
			return $this->getService($key);
		}
	}
	
	function isService($key) {
		return isset($this->services[$key]);
	}
	
	function getService($key) {
		return $this->services[$key]->getService();
	}
	
	function setService($key, ServiceContainer $service) {
		$this->services[$key] = $service;
		return $this;
	}
	
	// Example DDI of DB service
	function setDbService($host, $database, $user, $password) {
		$this->setService('db', new DbServiceContainer($host, $database, $user, $password));
	}
	
	public function newModelMapper($model) {
		$modelMapperClass = ucfirst($model).'Mapper';
		$modelMapper = new $modelMapperClass;
		$modelMapper->setDatabaseAdapter($this->getService('db'));
		return $modelMapper;
	}
}

class ServiceContainer {
	protected $service;
	
	function getService() {
		return isset($this->service) ? $this->service : $this->service = $this->startService();
	}
	
	function startService() {
		return null;
	}
	
	function isStarted() {
		return isset($this->service);
	}
	
	function stopService() {
		
	}
}

class DbServiceContainer extends ServiceContainer {
	protected $host, $database, $user, $password;
	
	function __construct($host, $database, $user, $password) {
		$this->host = $host;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
	}
	
	function startService() {
		return new DatabaseAdapter($this->host, $this->database, $this->user, $this->password);
	}
	
	function stopService() {
		$this->service->close();
	}
}