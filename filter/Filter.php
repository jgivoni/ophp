<?php

namespace Ophp;

/**
 * Simple filter abstract
 */
abstract class Filter implements FilterInterface {

	/**
	 * Filters the value
	 * 
	 * @param mixed $value
	 * @throws InvalidArgumentException
	 */
	final public function __invoke($value) {
		return $this->filter($value);
	}

}
