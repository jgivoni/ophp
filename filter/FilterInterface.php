<?php

namespace Ophp;

/**
 * A filter validates and sanitized a set of data
 * 
 * It either returns the santized data or throws an exception if the data is invalid
 * 
 * A filter is an executable, which can be made up of other filters
 */

/**
 * Example int filter
 */
//$int = '9';

interface FilterInterface
{
	/**
	 * Initialized a new filterint process
	 * 
	 * Stores value internally as original value,
	 * and calls init()
	 * 
	 * @param mixed $value
	 */
	public function __invoke($value);
	
	/**
	 * Converts the value to a well-formed value
	 * 
	 * Takes internally stored original value and
	 * saves well-formed value internally,
	 * without overwriting original value
	 * 
	 * When overriding this method, you must end by calling parent::init()
	 * and returning $this
	 * 
	 * @return Filter
	 */
	public function init();
	
	/**
	 * Returns a prepared (well-formed) value
	 */
	public function prep($value);
	/**
	 * Checks if a value is valid
	 * 
	 * Takes the well-formed value and inspects it according to the rules of
	 * the filter
	 * 
	 * Stores the result (true or false) internally and
	 * returns it as well
	 * 
	 * @return bool
	 */
	public function isValid();
	
	/**
	 * Checks if the value is valid
	 * @return bool
	 */
	public function check($value);
	
	public function filter();
	
	/**
	 * If not valid, sanitized the value and stores it internally as
	 * valid value
	 * 
	 * Returns $this
	 * 
	 * @return Filter
	 */
	public function sanitize($value);
}
