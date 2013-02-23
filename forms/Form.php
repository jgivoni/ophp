<?php

namespace Ophp;

/**
 *  Not sure this should be used - check out TaskFilter instead
 * 
 */
class Form
{
	protected $fields = array();
	
	public function __construct()
	{
	}

	public function addField($field)
	{
		$this->fields[$field->getName()] = $field;
	}
	
	public function getField($name)
	{
		
	}

}

