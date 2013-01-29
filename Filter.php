<?php

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


/**
 * Simple filter abstract
 */
abstract class Filter
{
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
	 * Returns the filtered value
	 * The value will pass through the filter only if it's valid
	 * Furthermore it will be passed through a sanitizer function
	 * 
	 * @param mixed $value
	 * @param string $mode
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	final public function filter($value, $mode = self::MODE_MUST_VALIDATE) {
		if (!$this->isValid($value)) {
			if ($mode == self::MODE_MUST_VALIDATE) {
				throw new InvalidArgumentException();
			} else {
				$value = $this->sanitize($value);
			}
		}
		return $value;
	}
	
	/**
	 * Checks if the value is valid
	 * 
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		return false;
	}
	
	/**
	 * Returns a valid value, based on the given value
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function sanitize($value) {
		return null;
	}
	
	/**
	 * Invokes the filter by calling the filter method
	 * 
	 * @param mixed $value
	 * @param string $mode
	 * @return mixed
	 */
	final public function __invoke($value, $mode = self::MODE_MUST_VALIDATE) {
		return $this->filter($value, $mode);
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
	}
	
	public function isValid($value)
	{
		foreach ($this->filters as $filter) {
			if (!$filter->isValid($value)) {
				return false;
			}
		}
		return true;
	}
	
	public function sanitize($value) {
		foreach ($this->filters as $filter) {
			$value = $filter->sanitize($value);
		}
		return $value;
	}
	
	
}

/**
 * Parameter filter to validate a set of parameters
 * Any parameter
 */
class ParamsFilter extends AggregateFilter {
	public function addParamFilter($key, Filter $filter) {
		$this->addFilter(new ParamFilter($key, $filter));
	}
}

/**
 * A filter that will act on an element of an indexed array
 */
class ParamFilter extends Filter {
	protected $key;
	protected $filter;
	
	public function __construct($key, Filter $filter) {
		$this->key = $key;
		$this->filter = $filter;
	}
	
	public function filter($params) {
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		try {
			$params[$this->key] = $this->filter->filter($value);
		} catch (InvalidArgumentException $e) {
			throw new InvalidArgumentException("Parameter '{$this->key}' invalid", null, $e);
		}
		return $params;
	}
	
	public function sanitize($params) {
		$value = isset($params[$this->key]) ? $params[$this->key] : null;
		$params[$this->key] = $this->filter->sanitize($value);
		return $params;
	}
	
	public function reset($value) {
		
	}
	
}
/**
 * A validator checks if something can pass through the filter
 */
abstract class ValidateFilter extends Filter
{
	final function sanitize($value)
	{
		throw new BadMethodCallException('Cannot sanitize a validation filter');
	}
	
	abstract public function reset($value);
}

/**
 * A sanitizer makes something passable though the filter
 */
class SanitizeFilter extends Filter
{
	protected $filter;
	
	public function __construct(Filter $filter) {
		$this->filter = $filter;
	}

	public function filter($value) {
		return $this->sanitize($value);
	}

	public function sanitize($value) {
		return $this->filter->sanitize($value);
	}
}

class RequiredFilter extends ValidateFilter {
	 public function filter($value)
	 {
		 if (!isset($value)) {
			 throw new InvalidArgumentException('Value required');
		 }
		 return $value;
	 }
	 
	 public function reset($value) {
		 return null;
	 }
}

class StrMaxLengthFilter extends GateFilter {
	protected $length;
	
	public function __construct($length) {
		$this->length = (int) $length;
	}
	public function filter($value) {
		if (mb_strlen((string)$value) > $this->length) {
			throw new InvalidArgumentException("Length of value exceeded {$this->length} characters");
		}
		return $value;
	}
	
	public function sanitize($value) {
		return mb_substr($value, 0, $this->length);
	}
}

/**
 * Dependency filter will only validate the second filter if the first filter validates
 */
class DependencyFilter extends Filter {
	protected $ifFilter;
	protected $thenFilter;
	
	public function __construct(Filter $ifFilter, Filter $thenFilter)
	{
		$this->ifFilter = $ifFilter;
		$this->thenFilter = $thenFilter;
	}
			
	public function filter($value) {
		try {
			$value = $this->ifFilter->filter($value);
		} catch (InvalidArgumentException $e) {
			return $value;
		}
		$value = $this->thenFilter->filter($value);
		return $value;
	}
	
	public function sanitize($value)
	{
		try {
			$value = $this->ifFilter->filter($value);
		} catch (InvalidArgumentException $e) {
			return $value;
		}
		try {
			$value = $this->thenFilter->sanitize($value);
		} catch (BadMethodCallException $e) {
			$value = $this->ifFilter->reset($value);
		}
		return $value;
	}
}

class MutualDependencyFilter extends AggregateFilter {
	public function __construct(Filter $filter1, Filter $filter2)
	{
		$this->addFilter(new DependencyFilter($filter1, $filter2));
		$this->addFilter(new DependencyFilter($filter2, $filter1));
	}
}

/**
 * Example of a filter for a complicated model, receiving data from a form
 */
class ExampleModelFilter extends ParamsFilter {
	public function __construct()	{
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
				new ParamFilter('longitude', new RequiredFilter()), 
				new ParamFilter('latitude', new RequiredFilter())
		));
		
		// How do we prevent unexpected parameters to pass the filter?
		// How to make it optional to choke on unexpected parameters?
		// How to toggle whether to validate or sanitize?
	}
}