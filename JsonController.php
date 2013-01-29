<?php

abstract class JsonController extends Controller {
	
	const RESPONSE_JSON = 'json';
	
	protected function newResponse() {
		$res = new Response();
		$res->headers['Content-Type'] = 'application/javascript; charset=utf-8';
		return $res;
	}
	
}
