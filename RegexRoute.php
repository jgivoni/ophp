<?php

namespace Ophp;

/**
 * Regex based routing
 * 
 * The regex is compared against the url path and will return a controller
 * instance if matched
 * 
 * The controller can be initialised with captured groups from the url
 */
class RegexRoute extends Route {
	function __construct($regex, $function) {
		$this->routeMatcher = function($url) use ($regex) {
			$matches = array();
			if (preg_match('#'.$regex.'#u', ltrim(parse_url($url, PHP_URL_PATH), '/'), /*&*/$matches)) {
				return $matches;
			} else {
				return false;
			}
		};
		$this->controllerCreator = function($matches) use ($function){
			$controller = call_user_func_array($function, array_slice($matches, 1));
			return $controller;
		};
	}
}