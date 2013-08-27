<?php

namespace Ophp;

class FormFieldOption {
	public $value;
	public $label;
	
	public function __construct($value, $label = null) {
		$this->value = $value;
		$this->label = isset($label) ? (string) $label : (string) $value;
	}
}