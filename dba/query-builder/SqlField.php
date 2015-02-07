<?php

namespace Ophp;

use Ophp\SqlCriteriaBuilder as CB;

class SqlField extends SqlExpression {

	/**
	 *
	 * @var string
	 */
	protected $f; // Field name
	protected $t; // Table name (optional)
	
	function __construct($f, $t = null) {
		$this->f = $f;
		$this->t = $t;
	}

	function is($v) {
		return new SqlCriteriaNodeCompare($this, $v, CB::IS);
	}

	function less($v) {
		return new SqlCriteriaNodeCompare($this, $v, CB::LESS);
	}

	public function acceptAssembler($assemblerVisitor) {
		return $assemblerVisitor->assembleField($this->f, $this->t);
	}
}
