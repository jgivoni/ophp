<?php

namespace Ophp\Router;

/**
 * Regex based routing
 * 
 * The regex is compared against the url path and will return a controller
 * instance if matched
 * 
 * The controller can be initialised with captured groups from the url
 */
class RegexRoute extends Route {

	/**
	 * Function returns mixed if route is matches, false if not
	 * 
	 * @var Callable
	 */
	protected $regex;

	/**
	 *
	 * @var Callable
	 */
	protected $controllerClass;

	public function __construct($regex, $controllerClass) {
		$this->regex = $regex;
		$this->controllerClass = $controllerClass;
	}

	/**
	 * Checks if the input matches the route
	 * 
	 * @param mixed $input
	 * @return mixed Constraint to pass on to the controller factory
	 */
	public function matches($input) {
		$matches = [];
		if (preg_match('#' . $this->regex . '#u', ltrim(parse_url($input, PHP_URL_PATH), '/'), $matches)) {
			return array_slice($matches, 1);
		} else {
			return false;
		}
	}

	/**
	 * Returns a controller based on the given constraint (optional)
	 * 
	 * @param mixed $constraint
	 * @return Controller
	 */
	public function getController($constraint = null) {
		$r = new \ReflectionClass($this->controllerClass);
		return $r->newInstanceArgs($constraint);
	}

}
