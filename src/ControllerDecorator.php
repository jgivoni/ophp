<?php

namespace Ophp;

class ControllerDecorator implements ControllerInterface {
	protected $decoratedController;
	
	public function __construct($controller) {
		$this->decoratedController = $controller;
	}
	
	final public function __invoke() {
		$this->beforeInvoke();
		$res = $this->decoratedController->__invoke();
		return $this->afterInvoke($res);
	}
	
	public function beforeInvoke() {
	}
	
	/**
	 *
	 * @param HttpResponse $res
	 * @return HttpResponse 
	 */
	public function afterInvoke(HttpResponse $res) {
		return $res;
	}
}