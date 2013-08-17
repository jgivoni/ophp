<?php

namespace Ophp;

/**
 * A mutual dependency filter finds a value valid if both filters find it
 * valid or both filters throw an exception
 */
class MutualDependencyFilter extends Filter {

	/**
	 *
	 * @var Filter
	 */
	protected $filter1;

	/**
	 *
	 * @var Filter
	 */
	protected $filter2;

	public function __construct(Filter $filter1, Filter $filter2) {
		$this->filter1 = $filter1;
		$this->filter2 = $filter2;
	}

	public function filter($value) {
		try {
			$value = $this->filter1->filter($value);
		} catch (\InvalidArgumentException $e) {
			try {
				$value = $this->filter2->filter($value);
			} catch (\InvalidArgumentException $e) {
				return $value;
			}
			throw $e;
		}

		return $this->filter2->filter($value);
	}

}
