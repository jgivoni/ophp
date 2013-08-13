<?php

namespace Ophp;

/**
 * A collection of filters
 */
class AggregateFilter extends Filter
{

	/**
	 *
	 * @var array List of filters
	 */
	protected $filters = array();
	
	protected $invalidFilter;

	/**
	 * Adds a filter to the list of filters
	 * 
	 * @param Filter $filter
	 */
	public function addFilter(Filter $filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	public function prep($value)
	{
		foreach ($this->filters as $filter) {
			$filter->prep($value);
		}
		return $value;
	}

	public function check($value)
	{
		foreach ($this->filters as $filter) {
			if (!$filter->check($value)) {
				$this->invalidFilter = $filter;
				return false;
			}
		}
		return true;
	}

	public function sanitize($value)
	{
		foreach ($this->filters as $filter) {
			$value = $filter->sanitize($value);
		}
		return $value;
	}

	public function getMessage()
	{
		return $this->invalidFilter->getMessage();
	}
}
