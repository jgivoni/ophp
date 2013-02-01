<?php

namespace Ophp;

class InputParams {
	protected $params = array();
	
	public function __construct(array $params) {
		$this->setParams($params);
	}
	
	public function setParams(array $params) {
		$this->params = $params;
	}
	
	public function __get($key) {
		return isset($this->params[$key]) ? new InputParam($this->params[$key]) : null;
	}
	
	public function __isset($key) {
		return isset($this->params[$key]);
	}
}

class InputParam {
	protected $value;
	public function __construct($value) {
		$this->setValue($value);
	}
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function __get($filter) {
		;
	}
	
	public function __isset($filter) {
		return $this->__get($filter) !== null;
	}
}