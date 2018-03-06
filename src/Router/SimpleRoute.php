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
class SimpleRoute extends Route {

	/**
	 * 
	 * @var string
	 */
	protected $value;

	/**
	 *
	 * @var string
	 */
	protected $controllerClass;

	public function __construct($value, $controllerClass) {
		$this->value = $value;
		$this->controllerClass = $controllerClass;
	}

	/**
	 * Checks if the input matches the route
	 * 
	 * @param mixed $input
	 * @return mixed Constraint to pass on to the controller factory
	 */
	public function matches($input) {
		return $input === $this->value;
	}

	/**
	 * Returns a controller based on the given constraint (optional)
	 * 
	 * @param mixed $constraint
	 * @return Controller
	 */
	public function getController($constraint = null) {
		return new $this->controllerClass;
	}

}
