<?php

namespace Ophp;

class DynamoDbDatabaseAdapter {

	/**
	 *
	 * @var \Aws\DynamoDb\DynamoDbClient
	 */
	protected $client;
	protected $region, $table;

	public function __construct($region, $table) {
		$this->region = $region;
		$this->table = $table;
	}

	/**
	 * @param string $sql
	 * @returns DbQueryResult
	 */
	public function query($sql) {
		$this->connect();

		/* @var $result \PDOStatement */
		try {
			$result = $this->connectionLink->query((string) $sql);
		} catch (\PDOException $e) {
			throw new \Exception("Couldn't execute SQL statement: \n" .
				$e->getMessage() . "\nSQL: '" . $sql . "'");
		}
		
		$dbQueryResult = new DbQueryResult(function() use ($result) {
					return $result->fetch();
				});
		$dbQueryResult->setNumRows($result->rowCount());

		return $dbQueryResult;
	}

	public function escapeString($str) {
		$this->connect();
		return $this->connectionLink->quote($str, \PDO::PARAM_STR);
	}

	public function getInsertId() {
		return $this->connectionLink->lastInsertId();
	}

	/**
	 * Returns a prepared select query builder
	 * 
	 * Run run() on the query builder to execute the query
	 * 
	 * @param mixed array|string $fields
	 * @return SqlQueryBuilder_Select
	 */
	public function select($fields = array()) {
		$sql = new SqlQueryBuilder_Select;
		$sql->setDba($this);
		$sql->select($fields);
		return $sql;
	}
	
	/**
	 * Returns a prepared DELETE query builder
	 * 
	 * Run run() on the query builder to execute the query
	 * 
	 * @param mixed array|string $fields
	 * @return \SqlQueryBuilder_Delete
	 */
	public function delete() {
		$sql = new SqlQueryBuilder_Delete();
		$sql->setDba($this);
		return $sql;
	}
	
	/**
	 * Returns a prepared INSERT query builder
	 * 
	 * Run run() on the query builder to execute the query
	 * 
	 * @param mixed array|string $fields
	 * @return \SqlQueryBuilder_Insert
	 */
	public function insert() {
		$sql = new SqlQueryBuilder_Insert();
		$sql->setDba($this);
		return $sql;
	}
	
	/**
	 * Returns a prepared UPDATE query builder
	 * 
	 * Run run() on the query builder to execute the query
	 * 
	 * @param mixed array|string $fields
	 * @return \SqlQueryBuilder_Update
	 */
	public function update($part = null) {
		$sql = new SqlQueryBuilder_Update;
		$sql->setDba($this);
		$sql->update($part);
		return $sql;
	}
	
}
