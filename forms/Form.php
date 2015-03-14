<?php

namespace Ophp;

/**
 *  Not sure this should be used - check out TaskFilter instead
 * 
 */
abstract class Form {

	const METHOD_GET = 1;
	const METHOD_POST = 2;
	
	protected $name;
	protected $method = self::METHOD_POST;
	protected $exceptions = array();
	
	protected $fields = array();

	public function newField($name = null) {
		return new FormField($name);
	}

	public function addField(FormField $field) {
		$this->fields[$field->getName()] = $field;
		return $this;
	}

	/**
	 * 
	 * @param string $name
	 * @return FormField
	 */
	public function getField($name) {
		return $this->hasField($name) ? $this->fields[$name] : null;
	}

	public function hasField($name) {
		return isset($this->fields[$name]);
	}
	
	public function addExceptions($exceptions = array()) {
		foreach ($exceptions as $exception) {
			if ($exception instanceof ParamFilterException) {
				$key = $exception->getKey();
				if ($this->hasField($key)) {
					$this->getField($key)->addException($exception);
				}
			} else {
				$this->addException($exception);
			}
		}
	}
	
	public function addException($exception) {
		if ($exception instanceof AggregateFilterException) {
			$this->addExceptions($exception->getExceptions());
		}
		$this->exceptions[] = $exception;
		return $this;
	}
	
	public function hasExceptions() {
		return !empty($this->exceptions);
	}

	public function getExceptions() {
		return $this->exceptions;
	}
}
