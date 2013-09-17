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
	 * View for a css <link> element
	 * @var View
	 */
	protected $cssLinkElemView;
	
	protected $jsScriptElemView;
	
	/**
	 * Collection of css files to include in the document
	 * @var array
	 */
	protected $cssFiles = array(); 
	
	protected $jsFiles = array();
	
	/**
	 * Creates a new html document view
	 * @param string $template Full template file path + name
	 * @param \Closure $newViewFunction
	 */
	public function __construct($template, $templateBase) {
		parent::__construct($template, $templateBase);
		$this->cssLinkElemView = $this->fragment('elements/link.html');
		$this->jsScriptElemView = $this->fragment('elements/script.html');
	}
	
	public function addCssFile($path) {
		$this->cssFiles[] = $path; 
		return $this;
	}
	
	public function addJsFile($path) {
		$this->jsFiles[] = $path;
		return $this;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	public function render() {
		$this->assign([
			'cssFiles' => $this->cssFiles,
			'jsFiles' => $this->jsFiles,
		]);
		return parent::render();
	}
}
