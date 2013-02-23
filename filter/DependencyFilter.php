<?php

namespace Ophp;

/**
 * Dependency filter will only validate the second filter if the first filter validates
 */
class DependencyFilter extends Filter
{

	protected $ifFilter;
	protected $thenFilter;

	public function __construct(Filter $ifFilter, Filter $thenFilter)
	{
		$this->ifFilter = $ifFilter;
		$this->thenFilter = $thenFilter;
	}

	public function prep($value)
	{
		$value = $this->ifFilter->prep($value);
		return $this->thenFilter->prep($value);
	}

	public function check($value)
	{
		if ($this->ifFilter->check($value)) {
			return $this->thenFilter->check($value);
		} else {
			return true;
		}
	}

	public function sanitize($value)
	{
		return $this->thenFilter->sanitize($value);
	}

}
