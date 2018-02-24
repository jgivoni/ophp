<?php

namespace Ophp;

/**
 * Gives a default value to non-set values
 */
class DefaultFilter extends Filter {

	protected $defaultValue;

	public function __construct($defaultValue) {
		$this->defaultValue = $defaultValue;
	}

	public function filter($value) {
		return isset($value) ? $value : $this->defaultValue;
	}

}
