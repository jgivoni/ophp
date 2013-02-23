<?php

namespace Ophp;

class MutualDependencyFilter extends AggregateFilter
{
	public function __construct(Filter $filter1, Filter $filter2)
	{
		$this->addFilter(new DependencyFilter($filter1, $filter2));
		$this->addFilter(new DependencyFilter($filter2, $filter1));
	}

}
