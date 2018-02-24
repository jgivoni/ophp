<?php

namespace Ophp;

/**
 * Checks if the value is set, i.e. is not null
 */
class IssetFilter extends Filter
{
	public function filter($value)
	{
		if (isset($value)) {
			return $value;
		} else {
			throw new \InvalidArgumentException('Value not set');
		}
	}
}
