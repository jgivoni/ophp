<?php

namespace Ophp;

/**
 * Standardized mapping between data storage and model
 * 
 * This data mapper should be stateless and not hold any information particular to a
 * specific mapping request.
 * One data mapper per model class is meant to be shared between all models using the 
 * same database adapter
 */
abstract class DataMapper {

	/**
	 * @var MySqlDatabaseAdapter
	 */
	protected $dba;
	protected $sharedModels = array();
	protected $fields = array();
	protected $tableName = '';
	protected $primaryKey = '';

	public function __construct() {
		;
	}

	public function setDba(SqlDatabaseAdapterInterface $dba) {
		$this->dba = $dba;
		return $this;
	}

	/**
	 * Returns a new instance of the model
	 * 
	 * @return Model
	 */
	abstract public function newModel();

	/**
	 * Returns a reusable model of the data corresponding to the primary key
	 * 
	 * @param mixed $primaryKey
	 * @return \Model
	 */
	protected function getSharedModel($primaryKey) {
		return isset($this->sharedModels[$primaryKey]) ?
				$this->sharedModels[$primaryKey] :
				$this->sharedModels[$primaryKey] = $this->loadModelByPrimaryKey($primaryKey);
	}

	/**
	 * Stores a model as a reusable instance
	 * 
	 * @param Model $model
	 */
	protected function setSharedModel(Model $model) {
		$this->sharedModels[$model[$this->primaryKey]] = $model;
		return $this;
	}

	public function onLoad(SqlQueryBuilder_Select $query = null) {
		if (!isset($query)) {
			$query = $this->dba->select();
		}
		$query->from("`{$this->tableName}`");

		foreach ($this->fields as $name => $config) {
			$query->select(isset($config['column']) ? $config['column'] : $name);
		}
		return $query;
	}
	
	public function loadOne(SqlQueryBuilder_Select $query = null) {
		$query = $this->onLoad($query);
		$recordSet = $query->run();

		if ($recordSet->isEmpty()) {
			throw new \OutOfBoundsException('Row not found');
		}

		$model = $this->mapRowToModel($recordSet->first());
		$this->setSharedModel($model);
		return $model;
	}

	/**
	 * 
	 * @param mixed $pk
	 * @return Model
	 * @throws Exception
	 */
	public function loadByPrimaryKey($pk) {
		$query = $this->dba->select()
				->where('`' . $this->getPkColumn() . '` = ' . (int) $pk);
		return $this->loadOne($query);
	}

	/**
	 * 
	 * @return array Of Model
	 */
	public function loadAll(SqlQueryBuilder_Select $query = null) {
		$query = $this->onLoad($query);

		$query->countMatchedRows();

		$recordSet = $query->run();

		$models = array();
		foreach ($recordSet as $record) {
			$model = $this->mapRowToModel($record);
			$this->setSharedModel($model);
			$models[] = $model;
		}
		return $models;
	}

	/**
	 * Deletes a row
	 * 
	 * @param \Ophp\Model $model
	 */
	public function deleteByModel(Model $model) {
		$sql = $this->dba->delete()
				->where("`{$this->fields[$this->primaryKey]}` = " . $model->{$this->primaryKey});
		$result = $this->deleteByQuery($sql);
		if ($result->getNumRows() !== 1) {
			throw new Exception('Row not found');
		}
		return $this;
	}

	/**
	 * 
	 * @param SqlQueryBuilder_Delete $sql
	 * @return DbQueryResult
	 */
	public function deleteByQuery(SqlQueryBuilder_Delete $sql) {
		return $sql->from("`{$this->tableName}`")
						->run();
	}

	/**
	 * 
	 * @param array $row
	 * @return Model
	 */
	protected function mapRowToModel($row) {
		$model = $this->newModel();
		foreach ($this->fields as $modelField => $config) {
			$name = isset($config['column']) ? $config['column'] : $modelField;
			switch ($config['type']) {
				case 'int': isset($v) || $v = (int) $row[$name];
				case 'timestamp': isset($v) || $v = strtotime($row[$name]);
				default: isset($v) || $v = $row[$name];
			}
			$model->$modelField = $v;
			unset($v);
		}
		return $model;
	}

	protected function getPkColumn() {
		return isset($this->fields[$this->primaryKey]['column']) ?
				$this->fields[$this->primaryKey]['column'] :
				$this->primaryKey;
	}
}