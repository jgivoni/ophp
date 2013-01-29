<?php

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
	 * @param Response $res
	 * @return Response 
	 */
	public function afterInvoke(Response $res) {
		return $res;
	}
}