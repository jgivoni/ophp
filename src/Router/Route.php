<?php

namespace Ophp\Router;

/**
 * A route parses a request and determines if its controller will handle it
 * The route might also add parameters to the request determined by the url, if matched
 * 
 */
class Route {
	
	/**
	 * Function returns mixed if route is matches, false if not
	 * 
	 * @var Callable
	 */
	protected $routeMatcher;
	/**
	 *
	 * @var Callable
	 */
	protected $controllerFactory;

	/**
	 * Creates a new route with the given route matcher and controller factory functions
	 * 
	 * @param Closure $routeMatcher Function that returns constraint if route is matches, false if not
	 * @param Closure $controllerFactory Function that returns a controller object
	 */
	public function __construct(\Closure $routeMatcher, \Closure $controllerFactory) {
		$this->routeMatcher = $routeMatcher;
		$this->controllerFactory = $controllerFactory;
	}
	
	/**
	 * Checks if the input matches the route
	 * 
	 * @param mixed $input
	 * @return mixed Constraint to pass on to the controller factory
	 */
	public function matches($input) {
		$routerMatcher = $this->routeMatcher;
		return $routerMatcher($input);
	}
	
	/**
	 * Returns a controller based on the given constraint (optional)
	 * 
	 * @param mixed $constraint
	 * @return Controller
	 */
	public function getController($constraint = null) {
		$controllerFactory = $this->controllerFactory;
		return $controllerFactory($constraint);
	}
	
}
	