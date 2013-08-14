<?php

namespace Ophp;

class StringFilter extends Filter
{

	protected $encoding;

	public function __construct($encoding)
	{
		$this->encoding = (string) $encoding;
	}

	public function prep($value)
	{
		return (string) $value;
	}

	public function check($value)
	{
		return mb_check_encoding($value, $this->encoding);
	}

	public function sanitize($value)
	{
		return mb_convert_encoding($value, $this->encoding);
	}

}
