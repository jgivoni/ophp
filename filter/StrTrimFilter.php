<?php

namespace Ophp;

/**
 * Trims whitespace or other characters from the beginning and end of string
 */
class StrTrimFilter extends Filter {

	/**
	 *
	 * @var string
	 */
	protected $charlist;

	public function __construct($charlist) {
		$this->charlist = (string) $charlist;
	}

	public function filter($value) {
		return trim($value, $this->charlist);
	}

}
