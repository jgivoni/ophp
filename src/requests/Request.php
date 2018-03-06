<?php

namespace Ophp\requests;

/**
 * Encapsulates a generic request
 */
abstract class Request {

	public function setServer($server) {
		$this->server = $server;
	}

	public function setServerVars($server_vars) {
		$this->server_vars = $server_vars;
		return $this;
	}

	public function setHeaders($headers) {
		$this->headers = $headers;
		return $this;
	}

}
