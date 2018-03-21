<?php

namespace Ophp\requests;

/**
 * Encapsulates a generic request
 */
abstract class Request {

	private $server_vars;

	public function setServer($server) {
		$this->server = $server;
	}

	public function setServerVars($server_vars) {
		$this->server_vars = $server_vars;
		return $this;
	}

	public function getServerVar($name) {
		return isset($this->server_vars[$name]) ? $this->server_vars[$name] : null;
	}

	public function setHeaders($headers) {
		$this->headers = $headers;
		return $this;
	}

}
