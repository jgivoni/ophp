<?php

class UrlHelper {
	protected $baseUrl;
	protected $paths = array();
	
	public function __construct($baseUrl) {
		$this->setBaseUrl($baseUrl);
	}

	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
	}
	
	public function __set($name, $value) {
		$this->paths[$name] = new UrlHelper($this->baseUrl . $value);
	}
	
	public function __call($name, $arguments) {
		return $this->paths[$name] . $arguments[0];
	}

	public function __toString() {
		return $this->baseUrl;
	}
	
	public function __invoke($path) {
		return $this->baseUrl . $path;
	}
}