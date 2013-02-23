<?php

namespace Ophp;

class StrMaxLengthFilter extends Filter
{

	protected $length;

	public function __construct($length)
	{
		$this->length = (int) $length;
	}

	public function prep($value)
	{
		return (string) $value;
	}

	public function check($value)
	{
		return mb_strlen($value) <= $this->length;
	}

	public function sanitize($value)
	{
		return mb_substr($value, 0, $this->length);
	}

}
