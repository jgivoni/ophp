<?php

namespace Ophp;

/**
 * Checks that a string is not too long
 */
class StrMaxLengthFilter extends Filter {

	/**
	 *
	 * @var int
	 */
	protected $length;

	public function __construct($length) {
		$this->length = (int) $length;
	}

	public function filter($value) {
		if (mb_strlen($value) <= $this->length) {
			return $value;
		} else {
			throw new \InvalidArgumentException('String too long');
		}
	}

}
