<?php

namespace Ophp;

class FormFieldOption {
	public $value;
	public $label;
	protected $isDisabled;
	
	public function __construct($value, $label = null) {
		$this->value = $value;
		$this->label = isset($label) ? (string) $label : (string) $value;
	}
	
	public function isDisabled() {
		return $this->isDisabled;
	}
	
	public function setDisabled($disabled = true) {
		$this->isDisabled = (bool) $disabled;
		return $this;
	}
}