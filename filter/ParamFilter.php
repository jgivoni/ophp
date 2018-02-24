<?php

namespace Ophp;

/**
 * A filter that will act on an element of an indexed array
 */
class ParamFilter extends Filter {

	/**
	 *
	 * @var string
	 */
	protected $key;

	/**
	 *
	 * @var Filter
	 */
	protected $filter;

	/**
	 *
	 * @var ParamsFilter
	 */
	protected $parent;

	/**
	 * 
	 * @param string $key They key the filter will operate on
	 * @param \Ophp\Filter $filter
	 * @param ParamsFilter The filter that controls all the parameters
	 */
	public function __construct($key, Filter $filter, ParamsFilter $parent = null) {
		$this->key = $key;
		$this->filter = $filter;
		$this->parent = $parent;
	}

	public function filter($params) {
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		try {
			$value = $this->filter->filter($value);
		} catch (FilterException $e) {
			$exception = new ParamFilterException('Parameter value violated', null, $e);
			$exception->setKey($this->key);
			throw $exception;
		}
		isset($this->parent) && $this->parent->keyFiltered($this->key);
		$params[$this->key] = $value;
		return $params;
	}

}

class ParamFilterException extends FilterException {
	protected $key;
	public function setKey($key) {
		$this->key = $key;
		return $this;
	}
	
	public function getKey() {
		return $this->key;
	}
}