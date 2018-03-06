<?php

namespace Ophp\dba;

/**
 * Decorates DynamoDb
 */
class CachedDynamoDbDatabaseAdapter extends DynamoDbDatabaseAdapter {

	/**
	 * Inserts a new item into a table
	 * 
	 * @param string $table
	 * @param array $item
	 * @param string $partitionKey Name of the partition key column/attribute
	 * @returns string Primary Key
	 */
	public function insert($table, $item, $partitionKey) {
		$this->saveToCache($item);
		return parent::insert($table, $item, $partitionKey);
	}

	public function updateAttributes($table, $primaryKey, $item) {
		$this->removeFromCache($item);
		return parent::updateAttributes($table, $primaryKey, $item);
	}

	public function delete($table, $item, $partitionKey) {
		$this->removeFromCache($item);
		return parent::delete($table, $item, $partitionKey);
	}

	/**
	 * 
	 * @param string $table
	 * @param array $key attribute=>value pairs
	 * @return type
	 * @throws \Exception
	 */
	public function get($table, $key) {
		$item = parent::get($table, $key);
		$this->saveToCache($item);
		return $item;
	}

	/**
	 * Adds elements to a string set or number set
	 * 
	 * All the elements for each attribute (1st level only) must be of the same type
	 * 
	 * @param type $table
	 * @param type $key
	 * @param type $elements
	 * @return boolean
	 * @throws \Exception
	 */
	public function addSetElements($table, $key, $elements) {
		$this->removeFromCache($key);
		return parent::addSetElements($table, $key, $elements);
	}

	/**
	 * Remove elements to a string set or number set
	 * 
	 * All the elements for each attribute (1st level only) must be of the same type
	 * 
	 * @param type $table
	 * @param type $key
	 * @param type $elements
	 * @return boolean
	 * @throws \Exception
	 */
	public function removeSetElements($table, $key, $elements) {
		$this->removeFromCache($key);
		return parent::removeSetElements($table, $key, $elements);
	}

}
