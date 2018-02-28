<?php

namespace Ophp;

class ViewPrinter {

	protected $value;

	/**
	 * 
	 * @param mixed $value
	 * @return \Ophp\ViewPrinter
	 */
	public function __invoke($value) {
		if ($value instanceof ViewPrinter) {
			return $value;
		} else {
			$this->value = $value;
			return $this;
		}
	}

	public function chData() {
		echo htmlspecialchars($this->value, ENT_NOQUOTES, 'UTF-8');
		return $this;
	}

	public function html() {
		echo (string) $this->value;
		if ($this->value instanceof View) {
			$exception = $this->value->getToStringException();
			if (isset($exception)) {
				throw new \Exception('Failed to render view', 0, $exception);
			}
		}
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