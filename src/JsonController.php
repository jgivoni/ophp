<?php

namespace Ophp;

abstract class JsonController extends Controller {
	
	const RESPONSE_JSON = 'json';
	
	protected function newResponse() {
		$res = new JsonResponse();
		return $res;
	}
	
}
