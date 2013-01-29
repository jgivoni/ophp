<?php

class RegexRoute extends Route {
	
	function __construct($regex, $controller_name) {
		$this->routeMatcher = function($url) use ($regex) {
			if (preg_match('#'.$regex.'#u', parse_url($url, PHP_URL_PATH), $matches)) {
				return $matches;
			} else {
				return false;
			}
		};
		$this->controllerCreator = function($matches) use ($controller_name){
			$meta  = new ReflectionClass($controller_name);
			$controller = $meta->newInstanceArgs(array_slice($matches, 1));
			return $controller;
		};
		$this->reverseRoute = function($params) use ($regex) {
			
		};
	}
}

class RegexRoute2 extends Route {
	
	function __construct($regex, $function) {
		$this->routeMatcher = function($url) use ($regex) {
			if (preg_match('#'.$regex.'#u', ltrim(parse_url($url, PHP_URL_PATH), '/'), /*&*/$matches = array(''))) {
				return $matches;
			} else {
				return false;
			}
		};
		$this->controllerCreator = function($matches) use ($function){
			$controller = call_user_func_array($function, array_slice($matches, 1));
			return $controller;
		};
		$this->reverseRoute = function($params) use ($regex) {
			
		};
	}
}