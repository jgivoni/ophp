<?php

namespace Ophp\Router;

class UrlRouter extends Router {
	/**
	 * Returns the controller of the first matching route, based only on the url
	 * 
	 * @param HttpRequest $req
	 * @return Controller
	 */
	public function getController(\Ophp\requests\HttpRequest $req) {
		foreach ($this->routes as $route) {
			$matches = $route->matches($req->url);
			if ($matches !== false) {
				$controller = $route->getController($matches);
				if ($controller instanceof MiddlewareController && $controller->continue()) {
					continue;
				} elseif (isset($controller)) {
					return $controller;
				}
			}
		}
		throw new NotFoundException('No route found for url: '.$req->url);
	}
}
