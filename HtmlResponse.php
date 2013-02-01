<?php

namespace Ophp;

class HtmlResponse extends HttpResponse {
	public $type = 'html';
	
	public function __construct() {
		$this->header('Content-Type', 'text/html; charset=utf-8');
	}
}