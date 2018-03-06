<?php

namespace Ophp\Router;

/**
 * A router takes a url and finds a controller that will handle the request
 *  
 */
class CommandRouter extends Router {
	/**
	 * Returns the controller of the first matching route, based only on the url
	 * 
	 * @param HttpRequest $req
	 * @return Controller
	 */
	public function getController(\Ophp\requests\Request $req) {
		foreach ($this->routes as $route) {
			$matches = $route->matches($req->command);
			if ($matches !== false) {
				$controller = $route->getController($matches);
				if ($controller instanceof MiddlewareController && $controller->continue()) {
					continue;
				} elseif (isset($controller)) {
					return $controller;
				}
			}
		}
		throw new NotFoundException('No route found for command: '.$req->command);
	}
}
