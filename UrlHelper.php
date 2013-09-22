<?php

namespace Ophp;

/**
 * The url helper helps with writing urls to resources known to the application
 * 
 * The url helper must be setup with a base url and as many named (even nested) subpaths as you need.
 * 
 * 
 * Examples:
 * $url = new UrlHelper('http://example.com/');
 * $url->myModule = 'my-module/';
 * $url->myModule->javascript = 'js/';
 * 
 * echo $url->myModule->javascript('jquery.js');
 * 
 * You can also use it for services endpoints:
 * $paymentApi = new UrlHelper('https://service.com/api/payments/');
 * $client->setEndpoint($paymentApi('transaction')); // Where transaction is the function to call
 */
class UrlHelper {
	/**
	 * Base url with trailing slash if it's a directory
	 * @var string
	 */
	protected $baseUrl;
	
	/**
	 * Collection of abstracted, named paths under base url
	 * @var array
	 */
	protected $paths = array();
	
	/**
	 * Creates a new url helper
	 * @param string $baseUrl Base url with trailing slash
	 * @return UrlHelper
	 */
	public function __construct($baseUrl = null) {
		if (isset($baseUrl)) {
			$this->setBaseUrl($baseUrl);
		}
		return $this;
	}

	/**
	 * Sets or changes the base url
	 * @param string $baseUrl Base url with trailing slash
	 * @return UrlHelper
	 */
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = (string) $baseUrl;
		return $this;
	}

	/**
	 * Adds a sub path as a nested url helper
	 * @param string $key Key for sub path - restrictions: must be a valid php method name (i.e. no spaces or slashes)
	 * @param string $subpath With trailing slash
	 * @return UrlHelper
	 */
	public function addSubpath($key, $subpath) {
		$this->paths[(string)$key] = new UrlHelper($this->baseUrl . $subpath);
		return $this->getSubpathUrlHelper($key);
	}
	
	/**
	 * Returns the url helper for the sub path
	 * @param string $key
	 * @return UrlHelper
	 */
	public function getSubpathUrlHelper($key) {
		if (isset($this->paths[(string)$key])) {
			return $this->paths[(string)$key];
		} else {
			throw new \Exception('Subpath does not exist');
		}
	}
	/**
	 * Sets a sub path
	 * @param string $name Name of sub path - restrictions on name: must be a valid php method name (i.e. no spaces or slashes)
	 * @param string $value Sub path relative to base url
	 */
	public function __set($key, $subpath) {
		$this->addSubpath($key, $subpath);
	}

	/**
	 * Returns the url helper for the sub path
	 * @param string $key
	 * @return UrlHelper
	 */
	public function __get($key) {
		return $this->getSubpathUrlHelper($key);
	}
	
	/**
	 * Shortcut to __invoke on a sub path url helper
	 * Returns the full url of a resource within a sub path
	 * @param string $key Key of sub path
	 * @param array $arguments Array of arguments of which the first one must be the resource filename/path relative to the subpath
	 * @return string
	 */
	public function __call($key, $arguments) {
		$subpath = $this->getSubpathUrlHelper($key);
		$path = isset($arguments[0]) ? $arguments[0] : '';
		return $subpath($path);
	}

	/**
	 * Returns the base url
	 * @return string
	 */
	public function __toString() {
		return (string) $this->baseUrl;
	}
	
	/**
	 * Returns the full url to a resource within the base url path
	 * @param string $path End path
	 * @return string
	 */
	public function __invoke($path = '') {
		return (string) $this . $path;
	}
}