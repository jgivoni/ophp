<?php

namespace Ophp;

/**
 * Parameter filter to validate a set of parameters
 * 
 * Each parameter must have at least one validator
 */
class ParamsFilter extends AggregateFilter
{

	protected $keys = array();

	public function addParamFilter($key, Filter $filter)
	{
		$this->keys[] = $key;
		$this->addFilter(new ParamFilter($key, $filter));
	}

	public function prep($value)
	{
		// Cast as array
		if (!is_array($value)) {
			$value = (array) $value;
		}
		$value = parent::prep($value);

		// Removed unexpected parameters - but how?
		// Somehow mark when a param has been prepped
		// Make sure that dependency filters only mark on dependencies

		return $value;
	}

}
