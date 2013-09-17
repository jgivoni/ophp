<?php

namespace Ophp;

class View {

	protected $template;
	protected $params = array();
	protected $renderedView;
	protected $viewPrinter;
	protected $newViewFunction;
	protected $templateBase;
	protected $parent;
	
	public function __construct($template, $templateBase = null) {
		$this->template = $template;
		if (isset($templateBase)) {
			$this->setTemplateBase($templateBase);
		}
	}

	public function setParent($parent) {
		$this->parent = $parent;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function top() {
		return isset($this->parent) ? $this->parent : $this;
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

	protected function getParams()
	{
		return $this->params;
	}
		
	public function setTemplateBase($templateBase) {
		$this->templateBase = $templateBase;
	}
	
	protected function getTemplateBase() {
		return $this->templateBase;
	}
	
	protected function getFullTemplatePath() {
		return $this->templateBase . $this->template . '.php';
	}

	/**
	 * Returns the rendition of the specified template with the parameters given
	 * 
	 * @return string The rendered template
	 */
	public function render() {
		$p = $this->getViewPrinter();
		extract($this->getParams());
		ob_start();
		include $this->getFullTemplatePath();
		return $this->renderedView = ob_get_clean();
	}

	public function __toString() {
		return isset($this->renderedView) ? $this->renderedView : $this->render();
	}

	public function fragment($template) {
		$fragment = new View($template, $this->getTemplateBase());
		$fragment->setParent($this->top());
		return $fragment;
	}
	
	protected function getViewPrinter() {
		return isset($this->viewPrinter) ? 
			$this->viewPrinter :
			$this->viewPrinter = new ViewPrinter;
	}
}
