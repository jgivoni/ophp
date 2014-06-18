<?php

namespace Ophp;

class CacheEntry implements \Serializable {

	protected $store;
	protected $prefix;
	protected $key;
	protected $value;
	protected $expiry;
	protected $createTimestamp;
	protected $accessTimestamp;

	public function __construct($key = null) {
		if (isset($key)) {
			$this->setKey($key);
		}
	}
	
	public function setKey($key) {
		$this->key = (string) $key;
		return $this;
	}

	public function setPrefix($prefix) {
		$this->prefix = (string) $prefix;
		return $this;
	}

	public function getFullKey() {
		return $this->prefix . $this->key;
	}
	
	public function setStore($store) {
		$this->store = $store;
		return $this;
	}

	public function setValue($value) {
		$this->createTimestamp = time();
		$this->value = $value;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function setExpiry($expiryTimestamp) {
		$this->expiry = $expiryTimestamp;
		return $this;
	}

	public function setTtl($ttlSeconds) {
		$this->setExpiry(time() + $ttlSeconds);
		return $this;
	}

	public function save() {
		$this->store->save($this->getFullKey(), $this->serialize(), $this->expiry);
		return $this;
	}
	
	public function load() {
		$data = $this->store->load($this->getFullKey());
		if (isset($data)) {
			$this->unserialize($data);
			$this->accessTimestamp = time();
			if ($this->expiry < time()) {
				$this->value = null;
			}
		}
		return $this;
	}

	/**
	 * this function doesn't make sense
	 * @return \Ophp\CacheEntry
	 */
	public function touch() {
		$this->accessTimestamp = time();
		$ttl = $this->expiry - $this->createTimestamp;
		$this->setTtl($ttl);
		$this->save();
		return $this;
	}

	public function delete() {
		$this->store->delete($this->getFullKey());
		return $this;
	}

	public function serialize() {
		return serialize(array($this->value, $this->expiry, $this->createTimestamp, $this->accessTimestamp));
	}

	public function unserialize($serialized) {
		list($this->value, $this->expiry, $this->createTimestamp, $this->accessTimestamp) =
				unserialize($serialized);
	}

}

abstract class Cache {
	/**
	 *
	 * @var CacheStore
	 */
	protected $store;
	protected $prefix;
	public function newEntry($key = null) {
		$cacheEntry = new CacheEntry($key);
		$cacheEntry->setStore($this->store)
			->setPrefix($this->prefix)
			->delete();
		return $cacheEntry;
	}
	
	public function loadEntry($key) {
		$cacheEntry = new CacheEntry($key);
		$cacheEntry->setStore($this->store)
			->setPrefix($this->prefix)
			->load();
		return $cacheEntry;
	}
}

abstract class CacheStore {
	/**
	 * Saves a value under a key in the store
	 * @param string $key
	 * @param mixed $value
	 */
	public function save($key, $value, $expiryTimestamp) {
		
	}

	/**
	 * Loads a value by key from the store
	 * @param string $key
	 */
	public function load($key) {
		
	}

	/**
	 * Returns whether or not a value exists for the key
	 * @param string $key
	 */
	public function exists($key) {
		
	}

	/**
	 * Removes the value for the key in the store
	 * @param string $key
	 */
	public function delete($key) {
		
	}

	/**
	 * Refreshes access time for a key
	 * @param string $key
	 */
	public function touch($key) {
		
	}

	/**
	 * Sets the time when the key will expire
	 * @param string $key
	 * @param int $timestamp
	 */
	public function setExpiry($key, $timestamp) {
		
	}

	/**
	 * Sets a function to be used to invalidate a value
	 * @param store $key
	 * @param callable $isValid
	 */
	public function invalidate($key, $isValid) {
		$entry = $this->getCacheEntry($key);
	}

	/**
	 * Removes all exired values from the store
	 */
	public function purge() {
		
	}

}