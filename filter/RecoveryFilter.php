<?php

namespace Ophp;

/**
 * Recovery filter will validate the second filter if the first filter doesn't validate
 */
class RecoveryFilter extends Filter {

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
		} catch (FilterException $e) {
			return $this->thenFilter->filter($value);
		}
		return $value;
	}

}
