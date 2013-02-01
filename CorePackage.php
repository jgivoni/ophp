<?php

namespace Ophp;

class CorePackage {
	public function __construct() {
		$this->bootstrap();
	}
	
	protected function bootstrap() {
		spl_autoload_register(function($class){
			$paths = array(
				__NAMESPACE__.'\Server' => 'Server.php',
				__NAMESPACE__.'\Route' => 'Route.php',
				__NAMESPACE__.'\RegexRoute' => 'RegexRoute.php',
				__NAMESPACE__.'\BaseRoute' => 'Route.php',
				__NAMESPACE__.'\Router' => 'Router.php',
				__NAMESPACE__.'\UrlRouter' => 'Router.php',
				__NAMESPACE__.'\HttpRequest' => 'Request.php',
				__NAMESPACE__.'\LampRequest' => 'Request.php',
				__NAMESPACE__.'\HttpResponse' => 'Response.php',
				__NAMESPACE__.'\HtmlResponse' => 'HtmlResponse.php',
				__NAMESPACE__.'\JsonResponse' => 'JsonResponse.php',
				__NAMESPACE__.'\VesselException' => 'VesselException.php',
				__NAMESPACE__.'\ControllerInterface' => 'ControllerInterface.php',
				__NAMESPACE__.'\Controller' => 'Controller.php',
				__NAMESPACE__.'\Model' => 'Model.php',
				__NAMESPACE__.'\View' => 'View.php',
				__NAMESPACE__.'\PartialView' => 'View.php',
				__NAMESPACE__.'\HtmlDocumentView' => 'HtmlDocumentView.php',
				__NAMESPACE__.'\DataMapper' => 'DataMapper.php',
				__NAMESPACE__.'\Filter' => 'Filter.php',
				__NAMESPACE__.'\UrlHelper' => 'UrlHelper.php',
			);
			if (isset($paths[$class])) {
				require_once __DIR__.'/'.$paths[$class];
			}
		});
	}
}