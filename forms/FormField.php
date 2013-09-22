<?php

namespace Ophp;

class FormField {

	const TYPE_TEXT = 'text';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_SELECT = 'select';

	protected $name;
	protected $value;
	protected $label;
	protected $type;
	protected $options;
	protected $exceptions;

	public function __construct($name = null) {
		if (isset($name)) {
			$this->setName($name);
		}
	}

	public function setName($name) {
		$this->name = (string) $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	public function getType() {
		return $this->type;
	}

	public function setOptions($options) {
		$this->options = $options;
		return $this;
	}

	public function getOptions() {
		return $this->options;
	}

	public function addExceptions($exceptions) {
		foreach ($exceptions as $exception) {
			$this->addException($exception);
		}
	}

	public function addException($exception) {
		if ($exception instanceof ParamFilterException) {
			$exception = $exception->getPrevious();
		}
		if ($exception instanceof AggregateFilterException) {
			$this->addExceptions($exception->getExceptions());
		} else {
			$this->exceptions[] = $exception;
		}
		return $this;
	}

	public function getExceptions() {
		return $this->exceptions;
	}

	public function hasExceptions() {
		return !empty($this->exceptions);
	}

}