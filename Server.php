<?php

namespace Ophp;

/**
 * The server class is responsible for handling a requests and emitting a response
 * This is done via the internal router, which is used to find a controller that will process the request and
 * construct the response.
 * The server is, however, controller-agnostic.
 * The server is also the only entity that knows things specific to the webserver and its environment it's running on
 */
class Server {

	const RUNMODE_DEVELOPMENT = 'development';
	const RUNMODE_STAGING = 'staging';
	const RUNMODE_PRODUCTION = 'production';

	/**
	 * The app root base path
	 * This is used at the moment to specify the path to the view scripts,
	 */
	protected $appRootPath;
	
	/**
	 * Base url of application with trailing slash if it's a directory
	 * 
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * The router object that manages a list of routes
	 */
	protected $router;

	/**
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 *
	 * @var string Key defining the type of environment (development, production, staging etc.)
	 */
	protected $runMode = self::RUNMODE_DEVELOPMENT;
	protected $urlHelper;

	/**
	 * Constructs the server - nothing needed to initialize it, as it is a stand-alone thing
	 */
	public function __construct($appRootPath) {
		$this->setAppRootPath($appRootPath);
		$config = $this->getConfig();
		
		$this->baseUrl = $config->baseUrl;
		$this->setRunMode($config->runMode);
		
		set_error_handler(array($this,'errorHandler'));  
	}

	public function setAppRootPath($appRootPath) {
		$this->appRootPath = $appRootPath;
	}

	public function getAppRootPath() {
		return $this->appRootPath;
	}
	
	/**
	 * Request factory
	 * The server knows what kind of request is appropriate here
	 * @return LampRequest
	 */
	public function newRequest() {
		return new LampRequest(); // < HttpRequest
	}

	/**
	 * Handles a request by routing it to a controller and sending the response
	 * to the client
	 * 
	 * @param HttpRequest $req Request object
	 */
	public function handleRequest(HttpRequest $req = null) {
		try {
			if ($this->isDevelopment()) {
				new FirePhpPackage;
			}
			if (empty($req)) {
				$req = $this->newRequest();
				$req->setServerVars($_SERVER); // @todo Break this up into its parts
				$req->setHeaders(apache_request_headers()); // Requires PHP 5.4 if not running as apache module
				$req->autoDetect();
			}
			$req->setServer($this);
			$response = $this->getResponse($req);
			$this->sendResponse($response);
			//$this->shutDown();
		} catch (NotFoundException $e) {
			$response = (new HttpResponse())
				->status(HttpResponse::STATUS_NOT_FOUND)
				->header('Content-Type', 'text/plain')
				->body('404 Page Not Found');
			$this->sendResponse($response);
		} catch (\Exception $e) {
			$response = new HttpResponse();
			$response->status(HttpResponse::STATUS_INTERNAL_SERVER_ERROR);
			$response->header('Content-Type', 'text/plain');
			if ($this->isDevelopment()) {
				$response->body($e->getMessage() . "\n" . $e->getTraceAsString());
			} else {
				$response->body('This is not working...');
			}
			$this->sendResponse($response);
		}
	}

	/**
	 * Sends the response to the client
	 * 
	 * @param HttpResponse $res
	 */
	public function sendResponse(HttpResponse $res) {
		foreach ($res->headers as $key => $value) {
			if (!is_numeric($key)) {
				$header = "$key: $value";
			} else {
				$header = $value;
			}
			header($header);
		}
		echo (string) $res;
	}

	/**
	 * Returns the response to a given request
	 * 
	 * @param object $req Request object
	 * @return object Response object
	 */
	public function getResponse(HttpRequest $req) {
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
	 * 
	 * @return Router
	 */
	public function newRouter() {
		return new UrlRouter();
	}

	public function addRoute(Route $route) {
		$this->getRouter()->addRoute($route);
		return $this;
	}

	public function newException($message, $previous) {
		return new Exception($message, 0, $previous);
	}

	public function newMysqlDatabaseAdapter($key) {
		$config = $this->getConfig();
		$db = $config->databaseConnections[$key];
		$dba = new MysqlDatabaseAdapter($db['host'], $db['database'], $db['user'], $db['password']);
		if ($this->isDevelopment()) {
			$dba = new DbaDebugDecorator($dba);
		}
		return $dba;
	}

	protected function getConfig() {
		return isset($this->config) ? $this->config : $this->loadConfig();
	}

	/**
	 * @todo Reqwrite this - too much presumption
	 * Perhaps config could be a config object instead?
	 * @return type
	 */
	protected function loadConfig() {
		$this->config = new \EnvironmentConfig;
		return $this->config;
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

	public function getUrlHelper() {
		if (isset($this->urlHelper)) {
			return $this->urlHelper;
		} else {
			$this->urlHelper = new UrlHelper($this->baseUrl);
			$config = $this->getConfig();
			foreach ($config->paths as $key => $path) {
				$this->urlHelper->$key = $path;
			}
			return $this->urlHelper;
		}
	}

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        // Don't throw exception if error reporting is switched off
        if (error_reporting() == 0) {
            return;
        }
        // Only throw exceptions for errors we are asking for
        if (error_reporting() & $errno) {

            $exception = new Exception($errstr);//, 0, $errno, $errfile, $errline);
			throw $exception;
        }
	}
}