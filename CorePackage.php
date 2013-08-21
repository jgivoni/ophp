<?php

namespace Ophp;

class CorePackage {
	public function __construct() {
		$this->bootstrap();
		new FilterPackage;
	}
	
	protected function bootstrap() {
		spl_autoload_register(function($class){
			$paths = array(
				__NAMESPACE__.'\Server' => 'Server.php',
				__NAMESPACE__.'\Config' => 'Config.php',
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
				__NAMESPACE__.'\ViewFragment' => 'View.php',
				__NAMESPACE__.'\HtmlDocumentView' => 'HtmlDocumentView.php',
				__NAMESPACE__.'\DataMapper' => 'DataMapper.php',
				__NAMESPACE__.'\UrlHelper' => 'UrlHelper.php',
				__NAMESPACE__.'\ViewContext\HtmlContext' => 'view-helpers/HtmlContext.php',
				__NAMESPACE__.'\ViewPrinter' => 'ViewPrinter.php',
				__NAMESPACE__.'\FilterPackage' => 'filter/FilterPackage.php',
				__NAMESPACE__.'\Form' => 'forms/Form.php',
				__NAMESPACE__.'\FormField' => 'forms/FormField.php',
			);
			if (isset($paths[$class])) {
				require_once __DIR__.'/'.$paths[$class];
			}
		});
	}
}