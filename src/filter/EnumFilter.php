<?php

namespace Ophp;

class EnumFilter extends Filter {

	protected $enumValues;

	public function __construct($enumValues) {
		$this->enumValues = $enumValues;
	}

	public function filter($value) {
		if (in_array($value, $this->enumValues, true)) {
			return $value;
		} else {
			throw new FilterException('Value is not in allowed set.');
		}
	}

}
