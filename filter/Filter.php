<?php

namespace Ophp;

/**
 * Simple filter abstract
 */
abstract class Filter implements FilterInterface
{

	/**
	 * The original value to filter
	 * @var mixed
	 */
	private $originalValue;

	/**
	 * The well-formed value, after initialization
	 * @var mixed
	 */
	private $wellFormedValue;

	/**
	 * Whether or not prep has been called
	 * @var bool
	 */
	private $prepped = false;

	/**
	 * Whether or not the well-formed value is valid
	 * @var bool
	 */
	private $wellFormedIsValid;

	/**
	 * Sanitized, valid value
	 * @var mixed
	 */
	private $sanitizedValue;

	/**
	 * Whether value has been sanitized (or does not require sanitazion)
	 * @var type 
	 */
	private $sanitized = false;

	/**
	 * A validation message
	 * 
	 * @var string
	 */
	private $message;

	/**
	 * Initializes the filter with a value
	 * 
	 * Resets all internal properties
	 * 
	 * @param mixed $value
	 * @param string $mode
	 * @return Filter
	 * @throws InvalidArgumentException
	 */
	final public function __invoke($value)
	{
		$this->originalValue = $value;
		$this->wellFormedValue = null;
		$this->prepped = false;
		$this->sanitizedValue = null;
		$this->sanitized = false;
		$this->message = null;
		$this->wellFormedIsValid = null;
		return $this;
	}

	final public function init()
	{
		if (!$this->prepped) {
			$this->wellFormedValue = $this->prep($this->originalValue);
			$this->prepped = true;
		}
		return $this;
	}

	final public function isValid()
	{
		if (!isset($this->wellFormedIsValid)) {
			if (!$this->prepped) {
				$this->init();
			}
			$this->wellFormedIsValid = $this->check($this->wellFormedValue);
		}
		if (!$this->wellFormedIsValid) {
			$this->message = $this->getMessage();
		}
		return $this->wellFormedIsValid;
	}

	final public function filter()
	{
		if (!$this->sanitized) {
			if (!$this->isValid()) {
				$this->sanitizedValue = $this->sanitize($this->wellFormedValue);
			} else {
				$this->sanitizedValue = $this->wellFormedValue;
			}
			$this->sanitized = true;
		}
		return $this->sanitizedValue;
	}
	
	protected function getMessage()
	{
		return 'Invalid value';
	}
	
	public function errorMessage()
	{
		return $this->message . ': ' . print_r($this->wellFormedValue, true);
	}
}
