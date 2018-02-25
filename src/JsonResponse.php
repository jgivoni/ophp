<?php

namespace Ophp;

class JsonResponse extends HttpResponse {
	public $type = 'json';
	
	public function __construct() {
		$this->headers['Content-Type'] = 'application/json; charset=utf-8';
	}
	
	public function body($data) {
		$this->body = $this->encodeData($data);
		return $this;
	}
	
	protected function encodeData($data) {
		if (is_scalar($data)) {
			return $data;
		} elseif (is_array($data) || $data instanceof Iterator) {
			$arr = array();
			foreach ($data as $key => $value) {
				$arr[$key] = $this->encodeData($value);
			}
			return $arr;
		} elseif (is_object($data) && $data instanceof \ArrayAccess) {
            $arr = array();
			foreach ($data as $key => $value) {
				$arr[$key] = $this->encodeData($value);
			}
			return $arr;
		} elseif (is_object($data) && method_exists($data, '__toString')) {
			return (string) $data;
		} else {
			return null;
		}
	}


	public function __toString() {
		return json_encode($this->body);
	}
}