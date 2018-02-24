<?php

namespace Ophp\Router;

/**
 * A router takes a url and finds a controller that will handle the request
 *  
 */
abstract class Router {
	/**
	 *
	 * @var array Array of routes
	 */
	protected $routes = array();
	
	/**
	 * Adds a route to the array of routes in this router
	 * Routes will be placed in the order they are added
	 * 
	 * @param string $key Route identifier
	 * @param Route $route
	 * @return Router This router object
	 */
	public function addRoute(Route $route) {
		$this->routes[] = $route;
		return $this;
	}
	
	/**
	 * Finds the first matching route and returns its controller
	 * 
	 * The router loops through the routes in its list and traverses recursively down the first one that matches
	 * until a controller is found.
	 * 
	 * @param HttpRequest $req
	 * @return BaseController
	 */
	abstract function getController(\Ophp\requests\HttpRequest $req);
	
	/**
	 * Reverse route lookup
	 * Returns the path that the route identified by $key will match
	 * 
	 * @param string $key Route identifier
	 * @param array $params Parameters for the route to contruct the url path
	 */
	public function getUrlPath($key, $params) {
		if (isset($this->routes[$key])) {
			return $this->routes[$key]->getUrlPath($params);
		} 
		throw new Exception('No route with this name: '.$key);
	}
	
	/**
	 * Creates a new route object which can be added to this router
	 * 
	 * @return Route object
	 */
	public function newRoute() {
		return new BaseRoute();
	}
}
