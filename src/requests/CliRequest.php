<?php

namespace Ophp\requests;

/**
 * Encapsulates a generic http request
 */
class CliRequest extends Request {

	public $command;
	public $params = array();

	public function getParam($name) {
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}

	function autoDetect() {
		$arguments = $_SERVER['argv'];

		$this->command = basename(array_shift($arguments), '.php');

		// @todo Use library to parse command options
		$this->params = $arguments;
	}

}
