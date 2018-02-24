<?php

namespace Ophp\requests;

class LampRequest extends HttpRequest {

	function autoDetect() {
		$server_vars = $this->server_vars;
		$this->params['get'] = isset($_GET) ? $_GET : array();

		$this->url = (empty($server_vars['HTTPS']) ? 'http' : 'https') . '://' . $server_vars['HTTP_HOST'] . $server_vars['REQUEST_URI'];
		$this->method = $server_vars['REQUEST_METHOD'];

		if ($this->isPost()) {
			$this->params['post'] = $_POST;
		}
	}

}