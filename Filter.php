<?php

namespace Ophp;

/**
 * A filter validates and sanitized a set of data
 * 
 * It either returns the santized data or throws an exception if the data is invalid
 * 
 * A filter is an executable, which can be made up of other filters
 */

/**
 * Example int filter
 */
//$int = '9';

interface FilterInterface {

	/**
	 * Initialized a new filterint process
	 * 
	 * @param mixed $value
	 */
	public function __invoke($value);

	/**
	 * Converts the value to a well-formed value
	 * 
	 * @return Filter
	 */
	public function init();

	/**
	 * 
	 * @return bool
	 */
	public function isValid();

	/**
	 * 
	 * @return Filter
	 */
	public function sanitize();

	/**
	 * @return TBD
	 */
	public function getResult();

	/**
	 * @return mixed
	 */
	public function getFilteredData();
}

/**
 * Simple filter abstract
 */
abstract class Filter implements FilterInterface {
	/**
	 * When filtering in this mode, invalid data will cause an exception to be thrown
	 */

	const MODE_MUST_VALIDATE = 'validate';
	/**
	 * When filtering in this mode, invalid data will be sanitized
	 */
	const MODE_SANITIZE_ONLY = 'sanitize';
	const MODE_STRICT = true;
	const MODE_LAX = false;

	/**
	 * The value we're filtering
	 * @var mixed
	 */
	protected $value;

	/**
	 * The validation result
	 * 
	 * @var TBD
	 */
	protected $result;

	/**
	 * Initializes the filter with a value
	 * 
	 * @param mixed $value
	 * @param string $mode
	 * @return Filter
	 * @throws InvalidArgumentException
	 */
	final public function __invoke($value) {
		$this->value = $value;
		$this->init();
		return $this;
	}

	public function init() {
		$this->result = null;
		return $this;
	}

	public function getResult() {
		if (!isset($this->result)) {
			throw new Exception('You must run validation first');
		}
		return $this->result;
	}

	public function getFilteredData() {
		if ($this->getResult() !== true) {
			throw new Exception('Data has not been successfully filtered');
		}
		return $this->value;
	}

}

/**
 * A collection of filters
 */
class AggregateFilter extends Filter {

	/**
	 *
	 * @var array List of filters
	 */
	protected $filters = array();

	/**
	 * Adds a filter to the list of filters
	 * 
	 * @param Filter $filter
	 */
	public function addFilter(Filter $filter) {
		$this->filters[] = $filter;
		return $this;
	}

	public function init() {
		foreach ($this->filters as $filter) {
			$filter->init();
		}
		return parent::init();
	}

	public function isValid() {
		foreach ($this->filters as $filter) {
			if (!$filter->isValid($this->value)) {
				$this->result = $filter->result;
				return false;
			}
		}
		$this->result = true;
		return true;
	}

	public function sanitize() {
		$value = $this->value;
		foreach ($this->filters as $filter) {
			$value = $filter->sanitize($value);
		}
		return $value;
	}

}

/**
 * Parameter filter to validate a set of parameters
 * 
 * Each parameter must have at least one validator
 */
class ParamsFilter extends AggregateFilter {

	protected $keys = array();

	public function addParamFilter($key, Filter $filter) {
		$this->keys[] = $key;
		$this->addFilter(new ParamFilter($key, $filter));
	}

	public function init() {
		// Cast as array
		if (is_array(!$this->value)) {
			$this->value = (array) $this->value;
		}
		// Removed unexpected parameters
		foreach ($this->value as $key => $value) {
			if (!in_array($key, $this->keys)) {
				unset($this->value[$key]);
			}
		}
		return parent::init();
	}

}

/**
 * A filter that will act on an element of an indexed array
 */
class ParamFilter extends Filter {

	protected $key;
	protected $filter;

	/**
	 * 
	 * @param string $key They key the filter will operate on
	 * @param \Ophp\Filter $filter
	 */
	public function __construct($key, Filter $filter) {
		$this->key = $key;
		$this->filter = $filter;
	}

	public function init($params) {
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		$f = $this->filter;
		$f($value);
		parent::init();
		return $this;
	}

	public function isValid() {
		if ($this->filter->isValid()) {
			$this->result = true;
			return true;
		} else {
			$this->result = $this->filter->getResult();
			return false;
		}
	}

	public function sanitize() {
		$this->value = $this->filter->sanitize()->getFilteredData();
		return $this;
	}

}

class RequiredFilter extends Filter {

	public function isValid() {
		if (isset($this->value)) {
			$this->result = true;
			return true;
		} else {
			$this->result = 'Parameter missing';
			return false;
		}
	}

	public function sanitize() {
		if ($this->result !== true) {
			throw new Exception('There is no way to sanitize a missing required parameter');
		}
	}
}

class StrMaxLengthFilter extends Filter {

	protected $length;

	public function __construct($length) {
		$this->length = (int) $length;
	}

	public function isValid() {
		if (mb_strlen((string) $this->value) > $this->length) {
			$this->result = "String too long";
			return false;
		} else {
			$this->result = true;
			return true;
		}
	}

	public function sanitize() {
		$this->value = mb_substr($this->value, 0, $this->length);
		return $this;
	}

}

/**
 * Dependency filter will only validate the second filter if the first filter validates
 */
class DependencyFilter extends Filter {

	protected $ifFilter;
	protected $thenFilter;

	public function __construct(Filter $ifFilter, Filter $thenFilter) {
		$this->ifFilter = $ifFilter;
		$this->thenFilter = $thenFilter;
	}

	public function isValid() {
		if ($this->ifFilter->isValid()) {
			if ($this->thenFilter->isValid()) {
				$this->result = true;
				return true;
			} else {
				$this->result = "Invalid dependent value";
				return false;
			}
		} else {
			$this->result = true;
			return true;
		}
	}

	public function sanitize() {
		$this->value = $this->thenFilter->sanitize()->getFilteredData();
		return $this;
	}

}

class MutualDependencyFilter extends AggregateFilter {

	public function __construct(Filter $filter1, Filter $filter2) {
		$this->addFilter(new DependencyFilter($filter1, $filter2));
		$this->addFilter(new DependencyFilter($filter2, $filter1));
	}

}

/**
 * Example of a filter for a complicated model, receiving data from a form
 */
class ExampleModelFilter extends ParamsFilter {

	public function __construct() {
		$this->addParamFilter('description', new AggregateFilter(
				array(
			// Must be a string - will be cast as string
			new StringFilter(),
			// Max 50 characters
			new StrMaxLengthFilter(50),
				)
		));

		$this->addParamFilter('name', new AggregateFilter(
				array(
			new StringFilter(),
			// Only alphanumeric characters
			new AlphanumFilter(),
			// Must have a value (other than null)
			new RequiredFilter(),
			// Max length 20 characters
			new StrMaxLengthFilter(20),
			// Must not be an empty string
			new StrNotEmpty(),
				)
		));

		// The 'status' field must be either 'draft' or 'published'
		$this->addParamFilter('status', new EnumFilter(array('draft', 'published')));

		// The 'author' field is required (must not be null)
		$this->addParamFilter('author', new RequiredFilter());

		// One filter only evaluated if other filter validates - and vice versa
		$this->addFilter(new MutualDependencyFilter(
				new ParamFilter('longitude', new RequiredFilter()), new ParamFilter('latitude', new RequiredFilter())
		));

		// How do we prevent unexpected parameters to pass the filter?
		// How to make it optional to choke on unexpected parameters?
		// How to toggle whether to validate or sanitize?
	}

}