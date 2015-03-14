<?php

namespace Ophp;

/**
 * Checks that a value is a valid string in the specified encoding
 */
class StringFilter extends Filter {

	const DEFAULT_ENCODING = 'UTF-8';

	/**
	 *
	 * @var string
	 */
	protected $encoding;

	public function __construct($encoding = self::DEFAULT_ENCODING) {
		$this->encoding = (string) $encoding;
	}

	public function filter($value) {
		$type = gettype($value);
		if ($type !== 'string') {
			$origValue = $value;
			$value = (string) $value;
			if ($type === 'int' && intval($value) !== $origValue) {
				throw new FilterException('Not a string');
			} elseif ($type === 'bool' && boolval ($value) !== $origValue) {
				throw new FilterException('Not a string');
			} elseif ($type === 'float' && floatval($value) !== $origValue) {
				throw new FilterException('Not a string');
			}
		}

		if (!mb_check_encoding($value, $this->encoding)) {
			$encoding = mb_detect_encoding($value);
			$value = mb_convert_encoding($value, $this->encoding, $encoding);
		}
		return $value;
	}

}
