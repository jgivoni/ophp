<?php

namespace Ophp;

class StrTrimFilter extends Filter
{

	protected $character;

	public function __construct($character)
	{
		$this->character = (string) $character;
	}

	public function prep($value)
	{
		return (string) trim($value, $this->character);
	}

	public function check($value)
	{
		return true;
	}
	
	public function sanitize($value)
	{
		return $value;
	}

}
