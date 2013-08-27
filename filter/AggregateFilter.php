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
		$exception = null;
		foreach ($this->filters as $filter) {
			try {
				$value = $filter($value);
			} catch (FilterException $e) {
				if (!isset($exception)) {
					$exception = new AggregateFilterException('One or more filters violated');
				}
				$exception->addException($e);
			}
		}
		if (isset($exception)) {
			throw $exception;
		}
		return $value;
	}

}

class AggregateFilterException extends FilterException {
	protected $exceptions = array();
	public function addException(FilterException $e) {
		$this->exceptions[] = $e;
	}
}
