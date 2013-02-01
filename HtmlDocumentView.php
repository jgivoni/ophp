<?php

namespace Ophp;

class HtmlDocumentView extends View {
	
	protected $newViewFunction;
	protected $css_link_elem_view;
	protected $css_files = array(); 
	
	public function __construct($template, \Closure $newViewFunction) {
		parent::__construct($template);
		$this->newViewFunction = $newViewFunction;
		$this->css_link_elem_view = $this->newView('elements/link.html');
	}
	
	public function newView($template) {
		$newViewFunction = $this->newViewFunction;
		$view = $newViewFunction($template);
		$view->parent = $this;
		return $view;
	}
	
	protected function format($val) {
		$format = $this->getFormatter();
		return $format($val);
	}
	
	protected function getFormatter() {
		return new StringFormatter;
	}
	
	public function addCssFile($path) {
		$this->css_files[] = $path; 
		
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function render() {
		$head = array();
		
		foreach ($this->css_files as $path) {
			$head[] = $this->css_link_elem_view->assign(array('path' => $path))->render();
		}
		$head[] = '<script type="text/javascript" src="/static/task/tasks.js"></script>';
				
		$this->head = $head;
		return parent::render();
	}

}
