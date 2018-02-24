<?php

namespace Ophp;

/**
 * The server class is responsible for handling a requests and emitting a response
 * This is done via the internal router, which is used to find a controller that will process the request and
 * construct the response.
 * The server is, however, controller-agnostic.
 * The server is also the only entity that knows things specific to the webserver and the environment it's running in
 */
abstract class Server {

	const RUNMODE_DEVELOPMENT = 'development';
	const RUNMODE_STAGING = 'staging';
	const RUNMODE_PRODUCTION = 'production';

	/**
	 * The router object that manages a list of routes
	 */
	protected $router;

	/**
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * 
	 * @var string Key defining the type of environment (development, production, staging etc.)
	 */
	protected $runMode = self::RUNMODE_DEVELOPMENT; // Default run mode
    
	/**
	 * Constructs the server - nothing needed to initialize it, as it is a stand-alone thing
	 */
	public function __construct() {
		$config = $this->getConfig();
		
		$this->setRunMode($config->runMode);
		
		set_error_handler(array($this,'errorHandler'));  
	}

	/**
	 * Request factory
	 * The server knows what kind of request is appropriate here
	 * @return LampRequest
	 */
	public function newRequest() {
		return new requests\Request();
	}

	/**
	 * Handles a request by routing it to a controller and sending the response
	 * to the client
	 * 
	 * @param HttpRequest $req Request object
	 */
	public function handleRequest(requests\HttpRequest $req = null) {
		try {
			if (empty($req)) {
				$req = $this->newRequest();
				$req->setServerVars($_SERVER); // @todo Break this up into its parts
//				$req->setHeaders(apache_request_headers()); // Requires PHP 5.4 if not running as apache module
				$req->autoDetect();
			}
			$req->setServer($this);
			$response = $this->getResponse($req);
			$this->sendResponse($response);
			//$this->shutDown();
		} catch (\Exception $e) {
			$response = new Response();
			$response->status(Response::STATUS_ERROR);
			if ($this->isDevelopment()) {
				$response->body($e->getMessage() . "\n" . $e->getTraceAsString());
			} else {
				$response->body('Error');
			}
			$this->sendResponse($response);
		}
	}

	/**
	 * Sends the response to the client
	 * 
	 * @param HttpResponse $res
	 */
	public function sendResponse(Response $res) {
		echo (string) $res;
	}

	/**
	 * Returns the response to a given request
	 * 
	 * @param object $req Request object
	 * @return object Response object
	 */
	public function getResponse(requests\HttpRequest $req) {
		$this->request = $req;
		$controller = $this->getRouter()->getController($req);
		$controller->setServer($this);
		return $controller();
	}

	/**
	 * Returns the server's router object
	 * If the router does not exist, it will be created
	 * 
	 * @return Router
	 */
	protected function getRouter() {
		return isset($this->router) ? $this->router : $this->router = $this->newRouter();
	}

	/**
	 * Returns a new router object
	 * In this server, we use url-routers, which select routes only based on the url
	 * Override this in app server
	 * @return Router
	 */
	abstract public function newRouter();

	public function addRoute(Route $route) {
		$this->getRouter()->addRoute($route);
		return $this;
	}

	public function newException($message, $previous = null) {
		return new Exception($message, 0, $previous);
	}

	/**
	 * Returns the app configuration
	 * @return Config
	 */
	protected function getConfig() {
		if (!isset($this->config)) {
			$this->config = $this->newConfig();
		}
		return $this->config;
	}

	/**
	 * Returns a new config object
	 * Override this in app server
	 * @return \Ophp\Config
	 */
	protected function newConfig()
	{
		return new Config;
	}

	public function getRequest() {
		return $this->request;
	}

	public function setRunMode($runMode) {
		if (!empty($runMode)) {
			$this->runMode = $runMode;
		}
	}

	public function getRunMode() {
		return $this->runMode;
	}

	public function isDevelopment() {
		return $this->getRunMode() === self::RUNMODE_DEVELOPMENT;
	}

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        // Don't throw exception if error reporting is switched off
        if (error_reporting() == 0) {
            return;
        }
        // Only throw exceptions for errors we are asking for
        if (error_reporting() & $errno) {
            throw $this->newException($errstr, $errno);
        }
	}
}
