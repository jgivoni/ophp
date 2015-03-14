<?php

namespace Ophp;

class Config {
	 public function __construct()
	 {
	 }
	 
	 public function __get($name)
	 {
		 return isset($this->$name) ? $this->$name : null;
	 }
}