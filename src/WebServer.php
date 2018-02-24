<?php

namespace Ophp;

/**
 * The server class is responsible for handling a requests and emitting a response
 * This is done via the internal router, which is used to find a controller that will process the request and
 * construct the response.
 * The server is, however, controller-agnostic.
 * The server is also the only entity that knows things specific to the webserver and the environment it's running in
 */
class WebServer extends Server {

	/**
	 * Base url of application with trailing slash if it's a directory
	 * 
	 * @var string
	 */
	protected $baseUrl;

    /**
     *
     * @var type 
     */
	protected $urlHelper;
    
    public function __construct() {
        parent::__construct();
		
		$this->baseUrl = $this->config->baseUrl;
	}
    
    /**
	 * Request factory
	 * The server knows what kind of request is appropriate here
	 * @return LampRequest
	 */
	public function newRequest() {
		return new requests\HttpRequest();
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
//				$req->autoDetect();
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
				$response->body('503 Internal Server Error');
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
	 * Returns a new router object
	 * In this server, we use url-routers, which select routes only based on the url
	 * Override this in app server
	 * @return Router
	 */
	public function newRouter() {
		return new Router\UrlRouter();
	}
    
	public function getUrlHelper() {
		if (!isset($this->urlHelper)) {
			$this->urlHelper = new UrlHelper($this->baseUrl);
			$config = $this->getConfig();
			foreach ($config->paths as $key => $path) {
				$this->urlHelper->$key = $path;
			}
		}
		return $this->urlHelper;
	}
}
