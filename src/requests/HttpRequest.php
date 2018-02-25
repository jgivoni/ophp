<?php

namespace Ophp\requests;

/**
 * Encapsulates a generic http request
 */
class HttpRequest extends Request {

	public $url;
	public $params = array();
	public $server;
	protected $server_vars;
	protected $headers;
	protected $method;

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_HEAD = 'HEAD';


	public function setServer($server) {
		$this->server = $server;
	}

	public function getUrlPath() {
		$parts = parse_url($this->url);
		return $parts['path'];
	}

	public function setServerVars($server_vars) {
		$this->server_vars = $server_vars;
		return $this;
	}

	public function setHeaders($headers) {
		$this->headers = $headers;
		return $this;
	}

	public function isGet() {
		return $this->method == self::METHOD_GET;
	}

	public function isPost() {
		return $this->method == self::METHOD_POST;
	}

	public function isPut() {
		return $this->method == self::METHOD_PUT;
	}

	public function isHead() {
		return $this->method == self::METHOD_HEAD;
	}

	public function getParam($name) {
		return isset($this->params['get'][$name]) ? $this->params['get'][$name] : null;
	}

	public function getPostParam($name) {
		return isset($this->params['post'][$name]) ? $this->params['post'][$name] : null;
	}

	public function getPostParams() {
		return isset($this->params['post']) ? $this->params['post'] : array();
	}

	public function isAjax() {
		//return true;
		return isset($this->headers['X-Requested-With']) && $this->headers['X-Requested-With'] === 'XMLHttpRequest';
	}

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
