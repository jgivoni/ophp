<?php

class VesselPackage {
	public function __construct() {
		$this->bootstrap();
	}
	
	protected function bootstrap() {
		spl_autoload_register(function($class){
			$paths = array(
				'Server' => 'Server.php',
				'Route' => 'Route.php',
				'RegexRoute' => 'RegexRoute.php',
				'RegexRoute2' => 'RegexRoute.php',
				'BaseRoute' => 'Route.php',
				'Router' => 'Router.php',
				'UrlRouter' => 'Router.php',
				'Request' => 'Request.php',
				'HttpRequest' => 'Request.php',
				'LampHttpRequest' => 'Request.php',
				'Response' => 'Response.php',
				'HtmlResponse' => 'HtmlResponse.php',
				'JsonResponse' => 'JsonResponse.php',
				'VesselException' => 'VesselException.php',
				'ControllerInterface' => 'ControllerInterface.php',
				'Controller' => 'Controller.php',
				'View' => 'View.php',
				'PartialView' => 'View.php',
				'HtmlDocumentView' => 'HtmlDocumentView.php',
				'DataMapper' => 'DataMapper.php',
				'Filter' => 'Filter.php',
				'UrlHelper' => 'UrlHelper.php',
			);
			if (isset($paths[$class])) {
				require_once __DIR__.'/'.$paths[$class];
			}
		});
	}
}