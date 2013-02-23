<?php

namespace Ophp;

class FilterPackage {
	public function __construct() {
		$this->bootstrap();
	}
	
	protected function bootstrap() {
		spl_autoload_register(function($class){
			$paths = array(
				__NAMESPACE__.'\Filter' => 'Filter.php',
				__NAMESPACE__.'\FilterInterface' => 'FilterInterface.php',
				__NAMESPACE__.'\AggregateFilter' => 'AggregateFilter.php',
				__NAMESPACE__.'\ParamsFilter' => 'ParamsFilter.php',
				__NAMESPACE__.'\ParamFilter' => 'ParamFilter.php',
				__NAMESPACE__.'\DependencyFilter' => 'DependencyFilter.php',
				__NAMESPACE__.'\MutualDependencyFilter' => 'MutualDependencyFilter.php',
				__NAMESPACE__.'\RequiredFilter' => 'RequiredFilter.php',
				__NAMESPACE__.'\StrMaxLengthFilter' => 'StrMaxLengthFilter.php',
				__NAMESPACE__.'\EnumFilter' => 'EnumFilter.php',
			);
			if (isset($paths[$class])) {
				require_once __DIR__.'/'.$paths[$class];
			}
		});
	}
}