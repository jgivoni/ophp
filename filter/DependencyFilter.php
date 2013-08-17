<?php

namespace Ophp;

/**
 * Dependency filter will only validate the second filter if the first filter validates
 */
class DependencyFilter extends Filter {

	/**
	 *
	 * @var Filter
	 */
	protected $ifFilter;

	/**
	 *
	 * @var Filter
	 */
	protected $thenFilter;

	public function __construct(Filter $ifFilter, Filter $thenFilter) {
		$this->ifFilter = $ifFilter;
		$this->thenFilter = $thenFilter;
	}

	public function filter($value) {
		try {
			$value = $this->ifFilter->filter($value);
		} catch (\InvalidArgumentException $e) {
			return $value;
		}

		return $this->thenFilter->filter($value);
	}

}
