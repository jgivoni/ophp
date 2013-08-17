<?php

namespace Ophp;

/**
 * Truncates string to specified max length
 */
class StrTruncateFilter extends Filter {

	/**
	 *
	 * @var int
	 */
	protected $length;

	public function __construct($length) {
		$this->length = (int) $length;
	}

	public function filter($value) {
		return mb_substr($value, 0, $this->length);
	}

}
