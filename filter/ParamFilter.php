<?php

namespace Ophp;

/**
 * A filter that will act on an element of an indexed array
 */
class ParamFilter extends Filter
{

	protected $key;
	protected $filter;

	/**
	 * 
	 * @param string $key They key the filter will operate on
	 * @param \Ophp\Filter $filter
	 */
	public function __construct($key, Filter $filter)
	{
		$this->key = $key;
		$this->filter = $filter;
	}

	public function prep($params)
	{
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		$value = $this->filter->prep($value);
		$params[$this->key] = $value;
		return $params;
	}

	public function check($params)
	{
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		return $this->filter->check($value);
	}

	public function sanitize($params)
	{
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		return $this->filter->sanitize($value);
	}

}
