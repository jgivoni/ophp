<?php

/**
 * The server class is responsible for handling a requests and emitting a response
 * This is done via the internal router, which is used to find a controller that will process the request and
 * construct the response.
 * The server is, however, controller-agnostic.
 * The server is also the only entity that knows things specific to the webserver and its environment it's running on
 */
class Server {
	
	/**
	 * The base path for scripts
	 * This is used at the moment to specify the path to the view scripts,
	 */
	public $base_path;
	
	public $base_url;
	
	/**
	 * The router object that manages a list of routes
	 */
	protected $router;
	
	/**
	 * @var Request
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
	protected $environment;
	
	protected $urlHelper;
	
	/**
	 * Constructs the server - nothing needed to initialize it, as it is a stand-alone thing
	 */
	public function __construct($base_path){
		$this->base_path = $base_path;
		$this->base_url = apache_getenv('vessel.base_url'); // @todo Autodetect if missing
		$this->environment = apache_getenv('vessel.environment');
	}
	
	/**
	 * Request factory
	 * The server knows what kind of request is appropriate here
	 * @return Request
	 */
	public function newRequest() {
		return new LampHttpRequest();// < HttpRequest < Request
	}
	
	/**
	 * Handles a request by routing it to a controller and returning the response
	 * 
	 * @param Request $req Request object
	 */
	public function handleRequest(Request $req = null) {
		try {
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
		} catch (Exception $e) {
			$response = new Response();
			$response->status(Response::STATUS_INTERNAL_SERVER_ERROR);
			$response->header('Content-Type', 'text/plain');
			if ($this->isDevelopment()) {
				$response->body($e->getMessage());
			} else {
				$response->body('This is not working...');
			}
			$this->sendResponse($response);
		}
	}
	
	/**
	 * Sends the response to the client
	 * 
	 * @param Response $res
	 */
	public function sendResponse(Response $res) {
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
	public function getResponse(Request $req) {
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
		$db = $config['database_connections'][$key];
		return new MysqlDatabaseAdapter($db['host'], $db['database'], $db['user'], $db['password']);
	}
	
	protected function getConfig() {
		return isset($this->config) ? $this->config : $this->loadConfig();
	}
	
	protected function loadConfig() {		
		$this->config = include $this->base_path.'/config/'.$this->environment.'.php';
		return $this->config;
	}
	
	public function getRequest() {
		return $this->request;
	}
	
	public function isDevelopment() {
		return $this->environment == 'development';
	}
	
	public function getUrlHelper() {
		if (isset($this->urlHelper)) {
			return $this->urlHelper;
		} else {
			$this->urlHelper = new UrlHelper($this->base_url);
			$config = $this->getConfig();
			foreach ($config['paths'] as $key => $path) {
				$this->urlHelper->$key = $path;
			}
			return $this->urlHelper;
		}
	}
}