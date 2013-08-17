<?php

namespace Ophp;

/**
 * A collection of filters
 */
class AggregateFilter extends Filter {

	/**
	 *
	 * @var array List of filters
	 */
	protected $filters = array();

	public function __construct($filters = array()) {
		foreach ($filters as $filter) {
			$this->addFilter($filter);
		}
	}

	/**
	 * Adds a filter to the list of filters
	 * 
	 * @param Filter $filter
	 */
	public function addFilter(Filter $filter) {
		$this->filters[] = $filter;
		return $this;
	}

	public function filter($value) {
		$lastException = null;
		foreach ($this->filters as $filter) {
			try {
				$value = $filter($value);
			} catch (\InvalidArgumentException $e) {
				// TODO: How to catch all the expections in a chain?
				$lastException = $e;
			}
		}
		if (isset($lastException)) {
			throw $lastException;
		}
		return $value;
	}

}

