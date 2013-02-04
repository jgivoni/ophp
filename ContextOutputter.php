<?php

namespace Ophp;

class ContextOutputter {

	protected $value;

	public function __invoke($value) {
		$this->value = $value;
		return $this;
	}

	public function chData() {
		echo htmlspecialchars($this->value, ENT_NOQUOTES, 'UTF-8');
	}

	public function html() {
		echo $this->value;
		return $this;
	}

	public function attrVal() {
		echo htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8');
		return $this;
	}
	
	public function __toString() {
		trigger_error('Do not use '.__CLASS__.' in write context!', E_USER_WARNING);
		return "";
	}

}