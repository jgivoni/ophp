<?php

namespace Ophp;

/**
 * A filter sanitizes and/or validates a set of data
 * 
 * It either returns the santized data or throws an exception if the data is invalid
 * 
 * A filter is an executable, which can be made up of other filters
 */
interface FilterInterface {

	/**
	 * Invokes the filter function
	 * @param mixed $value
	 */
	public function __invoke($value);

	/**
	 * The function called by __invoke
	 * @param mixed $value
	 */
	public function filter($value);
}
