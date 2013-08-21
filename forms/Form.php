<?php

namespace Ophp;

/**
 *  Not sure this should be used - check out TaskFilter instead
 * 
 */
class Form {

	protected $fields = array();

	public function __construct() {
		
	}

	public function newField($name = null) {
		return new FormField($name);
	}

	public function addField(FormField $field) {
		$this->fields[$field->getName()] = $field;
		return $this;
	}

	public function getField($name) {
		return isset($this->fields[$name]) ? $this->fields[$name] : null;
	}

}

