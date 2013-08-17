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
		$value = $this->filter->filter($value);
		isset($this->parent) && $this->parent->keyFiltered($this->key);
		$params[$this->key] = $value;
		return $params;
	}

}
