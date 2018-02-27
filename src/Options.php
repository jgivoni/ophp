<?php

namespace Ophp;

class Options implements \ArrayAccess {

	protected $params = [];

	public function __construct($params = []) {
		$this->params = $params;
	}

	public function get($key) {
		if (!array_key_exists($key, $this->params)) {
			$this->params[$key] = new self;
		}
		return $this->params[$key];
	}

	public function set($key, $value) {
		$this->params[$key] = $value;
		return $this;
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function offsetExists($key): bool {
		return $this->__isset($key);
	}

	public function offsetGet($key) {
		return $this->get($key);
	}

	public function offsetSet($key, $value) {
		$this->set($key, $value);
	}

	public function offsetUnset($key) {
		$this->__unset($key);
	}

	public function __toString() {
		return '';
	}

	public function isNull() {
		return count($this->params) == 0;
	}

	public function __isset($key) {
		if (isset($this->params[$key])) {
			if ($this->params[$key] instanceof self && $this->params[$key]->isNull()) {
				$isset = false;
			} else {
				$isset = true;
			}
		}
		return isset($isset) ? $isset : false;
	}

	public function __unset($key) {
		unset($this->params[$key]);
	}

	/**
	 * Returns the parameters as a normal array
	 * 
	 * All elements of Param type are also recursively converted to an array
	 * 
	 * @param bool $recursive
	 */
	public function toArray() {
		return array_map(function($param) {
			return $param instanceof self ? $param->toArray() : $param;
		}, $this->params);
	}

	public function count() {
		return count($this->params);
	}

}
