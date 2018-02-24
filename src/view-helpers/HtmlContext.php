<?php

namespace Ophp\ViewContext;

class HtmlContext {
	
	function e($string) {
		return htmlspecialchars((string)$string);
	}
}
