<?php

namespace Ophp;

/**
 * Checks that a value is a valid integer
 */
class IntegerFilter extends Filter {

	public function filter($value) {
		$type = gettype($value);
		if ($type !== 'string') {
			$origValue = $value;
			$value = (int) $value;
			if ($type === 'string' && intval($value) !== $origValue) {
				throw new \InvalidArgumentException('Not an integer');
			} elseif ($type === 'bool' && (bool) value !== $origValue) {
				throw new \InvalidArgumentException('Not an integer');
			} elseif ($type === 'float' && floatval($value) !== $origValue) {
				throw new \InvalidArgumentException('Not an integer');
			}
		}
		return $value;
	}

}
