<?php

namespace Ophp;

/**
 * Parameter filter to validate a set of parameters
 * 
 * Each parameter must have at least one validator
 */
class ParamsFilter extends AggregateFilter {

	/**
	 * List of filtered keys
	 * @var array
	 */
	protected $keysFiltered = array();

	public function addParamFilter($key, Filter $filter) {
		$this->addFilter(new ParamFilter($key, $filter, $this));
	}

	public function filter($params) {
		// Cast as array
		if (!is_array($params)) {
			$params = (array) $params;
		}
		$params = parent::filter($params);

		foreach ($params as $key => $param) {
			if (!in_array($key, $this->keysFiltered)) {
				unset($params[$key]);
			}
		}
		return $params;
	}

	/**
	 * Callback function for ParamFilter to indicate that a key has been
	 * filtered
	 * @param string $key
	 */
	public function keyFiltered($key) {
		$this->keysFiltered[] = $key;
	}

}
