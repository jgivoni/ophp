<?php

namespace Ophp;

class IssetFilter extends Filter
{
	public function prep($value)
	{
		return $value;
	}

	public function check($value)
	{
		return isset($value);
	}

	public function sanitize($value)
	{
		throw new \Exception('There is no way to sanitize a missing required parameter');
	}
	
	public function getMessage()
	{
		return 'Value not set';
	}

}
