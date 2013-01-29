<?php

/**
 * A route parses a url and determines if its controller will handle it
 * The route might also add parameters to the request determined by the url, if matched
 * 
 */
class Route {
	
	/**
	 *
	 * @var Closure Function returns mixed if route is matches, false if not
	 */
	protected $routeMatcher;
	/**
	 *
	 * @var Closure Function returns a controller object
	 */
	protected $controllerCreator;
	/**
	 * @var Closure Function returns a url path
	 */
	protected $reverseRoute;

	public function __construct(Closure $routeMatcher, Closure $controllerCreator, Closure $reverseRoute = null) {
		$this->routeMatcher = $routeMatcher;
		$this->controllerCreator = $controllerCreator;
		$this->reverseRoute = $reverseRoute;
	}
	
	/**
	 * Checks if a url matches the route
	 * 
	 * @param string $url
	 * @return mixed True, false or any value to pass on to the $controllerCreator
	 */
	public function matches($url) {
		$routerMatcher = $this->routeMatcher;
		return $routerMatcher($url);
	}
	
	public function getController($value = null) {
		$controllerCreator = $this->controllerCreator;
		return $controllerCreator($value);
	}
	
	public function getUrlPath($params) {
		$reverseRoute = $this->reverseRoute;
		return $reverseRoute($params);
	}
}

/* generalize this:
new Route(function($url){
	if (preg_match('#\.t(\d+)$#', parse_url($url, PHP_URL_PATH), $matches)) {
		return $matches;
	} else {
		return false;
	}
}, function($matches){
	return new ViewTaskController($matches[1]);
}, function($params) {
	return '/.t'.$params['task_id'];
	*/
	