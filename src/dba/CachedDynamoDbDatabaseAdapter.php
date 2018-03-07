<?php

namespace Ophp\dba;

/**
 * Decorates DynamoDb
 */
class CachedDynamoDbDatabaseAdapter extends DynamoDbDatabaseAdapter {

	/**
	 *
	 * @var \Redis
	 */
	protected $cache;
	
	/**
	 * 
	 * @return \Redis
	 */
	protected function getCache() {
		if (!isset($this->cache)) {
			$this->cache = $this->options->cacheClient;
		}
		return $this->cache;
	}


	protected function saveToCache($item, $partitionKey) {
		$this->getCache()->set($item[$partitionKey], $item);
	}
	
	protected function removeFromCache($key) {
		$this->getCache()->del($key);
	}

	protected function getFromCache($key) {
		$item = $this->getCache()->get($key);
		return isset($item) && $item !== false ? $item : null;
	}

	/**
	 * Inserts a new item into a table
	 * 
	 * @param string $table
	 * @param array $item
	 * @param string $partitionKey Name of the partition key column/attribute
	 * @returns string Primary Key
	 */
	public function insert($table, $item, $partitionKey) {
		$this->saveToCache($item, $partitionKey);
		return parent::insert($table, $item, $partitionKey);
	}

	public function updateAttributes($table, $item, $partitionKey) {
		$this->removeFromCache($item[$partitionKey]);
		return parent::updateAttributes($table, $item, $partitionKey);
	}

	public function delete($table, $item, $partitionKey) {
		$this->removeFromCache($item[$partitionKey]);
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
		$item = $this->getFromCache(current($key));
		if (!isset($item)) {
			$item = parent::get($table, $key);
			$this->saveToCache($item, key($key));
		}
		return $item;
	}

	public function getBatch($table, $keys) {
		$items = [];
		$neededKeys = [];
		foreach ($keys as $key) {
			$item = $this->getFromCache(current($key));
			if (isset($item)) {
				$items[] = $item;
			} else {
				$neededKeys[] = $key;
			}
		}
		if (!empty($neededKeys)) {
			$loadedItems = parent::getBatch($table, $neededKeys);
			foreach ($loadedItems as $item) {
				$this->saveToCache($item, key(current($keys)));
			}
			$items = array_merge($items, $loadedItems);
		}
		return $items;
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
		$this->removeFromCache(current($key));
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
		$this->removeFromCache(current($key));
		return parent::removeSetElements($table, $key, $elements);
	}

}
