<?php

namespace Ophp;

/**
 * View for an entire html document (as opposed to just a fragment)
 * 
 * Understands certain parts of an html document and contains logic to populate
 * them.
 * 
 */
class HtmlDocumentView extends View {
	
	/**
	 * Closure that will return a new view - needs to be passed in by the controller
	 * @var Closure
	 */
	protected $newViewFunction;
	
	/**
	 * View for a css <link> element
	 * @var View
	 */
	protected $cssLinkElemView;
	
	/**
	 * Collection of css files to include in the document
	 * @var array
	 */
	protected $cssFiles = array(); 
	
	/**
	 * Creates a new html document view
	 * @param string $template Full template file path + name
	 * @param \Closure $newViewFunction
	 */
	public function __construct($template, \Closure $newViewFunction) {
		parent::__construct($template);
		$this->newViewFunction = $newViewFunction;
		$this->cssLinkElemView = $this->newView('elements/link.html');
	}
	
	/**
	 * Returns a new sub view and attaches it to this document
	 * The view is created via a closure passed in from the controller
	 * in order not to worry the view about global template paths.
	 * @param string $template
	 * @return View
	 */
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
		$this->cssFiles[] = $path; 
		
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function render() {
		$head = array();
		
		foreach ($this->cssFiles as $path) {
			$head[] = $this->cssLinkElemView->assign(array('path' => $path))->render();
		}
		$head[] = '<script type="text/javascript" src="/static-assets/task/tasks.js"></script>';
				
		$this->head = $head;
		return parent::render();
	}

}
