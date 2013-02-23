<?php

namespace Ophp;

class EnumFilter extends Filter
{
	protected $enumValues;
	public function __construct($enumValues)
	{
		$this->enumValues = $enumValues;
	}
	public function prep($value)
	{
		return $value;
	}

	public function check($value)
	{
		return in_array($value, $this->enumValues, true);
	}

	public function sanitize($value)
	{
		return null;
	}

}
