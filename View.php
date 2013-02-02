<?php

namespace Ophp;

class View {

	protected $template;
	protected $params = array();
	protected $renderedView;
	protected $contextOutputter;
	
	public function __construct($template) {
		$this->template = $template;
	}

	public function assign($params) {
		$this->params = array_merge($this->params, $params);
		return $this;
	}

	public function __set($key, $value) {
		$this->params[$key] = $value;
	}

	public function __get($key) {
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}

	/**
	 * Returns the rendition of the specified template with the parameters given
	 * 
	 * @return string The rendered template
	 */
	public function render() {
		$c = $this->getContextOutputter();
		extract($this->params);
		ob_start();
		include $this->template;
		return $this->renderedView = ob_get_clean();
	}

	public function __toString() {
		return isset($this->renderedView) ? $this->renderedView : $this->render();
	}
	
	protected function getContextOutputter() {
		return isset($this->contextOutputter) ? 
			$this->contextOutputter :
			$this->contextOutputter = new ContextOutputter;
	}

	// Iterator interface
	/*public function current() {
		return current($this->params);
	}
	public function key() {
		return key($this->params);
	}
	public function next() {
		return next($this->params);
	}
	public function rewind() {
		return reset($this->params);
	}
	public function valid() {
		return key($this->params);
	}*/
}

class PartialView extends View {

	protected $parent;

	public function attachToParent($parentView, $assignAs) {
		$this->parent = $parentView;
		$this->parent->assign(array($assignAs => $this));
	}

	/**
	 * Tries to render from the top view, but avoiding infinite recursion...
	 * 
	 * @return string
	 */
	public function render() {
		parent::render();
		return $this->parent->render();
	}

	// Iterator interface
	public function current() {
		return current($this->parent->params);
	}
	public function key() {
		return key($this->parent->params);
	}
	public function next() {
		return next($this->parent->params);
	}
	public function rewind() {
		return reset($this->parent->params);
	}
	public function valid() {
		return key($this->parent->params);
	}
}

class ViewDecorator {

}
