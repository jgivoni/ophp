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
	 *
	 * @var SqlCriteriaAssembler
	 */
	protected $sharedModels = array();
	protected $fields = array();
	protected $tableName = '';
	protected $primaryKey = '';

	public function setDba($dba) {
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
	 * Returns a reusable model of the data record corresponding to the primary key
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

	/**
	 * Returns a model for the first row matched
	 * @param \Ophp\SqlQueryBuilder_Select $query
	 * @return Model
	 * @throws \OutOfBoundsException
	 */
	abstract public function loadOne($query);

	/**
	 * Returns the count of rows matched by the select query
	 * @param \Ophp\SqlQueryBuilder_Select $query
	 * @return int
	 */
	abstract public function count($query);

	/**
	 * 
	 * @param mixed $pk
	 * @return Model
	 * @throws Exception
	 */
	abstract public function loadByPrimaryKey($pk);

	/**
	 * 
	 * @return array Of Model
	 */
	public function loadAll($query = null) {
		$recordSet = $this->dba->query($query);

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
	abstract public function deleteByModel(Model $model);

	/**
	 * 
	 * @param array $row
	 * @return Model
	 */
	protected function mapRowToModel($row) {
		$model = $this->newModel();
		foreach ($this->fields as $modelField => $config) {
			$name = isset($config['column']) ? $config['column'] : $modelField;
			if (array_key_exists($name, $row)) {
				switch ($config['type']) {
					case 'int':
						$v = (int) $row[$name];
						break;
					case 'timestamp':
						$v = strtotime($row[$name]);
						break;
					case 'array':
						if (is_array($row[$name])) {
							$v = $row[$name];
						} elseif (method_exists($row[$name], 'toArray')) {
							$v = $row[$name]->toArray();
						}
						break;
					default:
						$v = $row[$name];
				}
				$model->$modelField = $v;
			}
			unset($v);
		}
		return $model;
	}

	/**
	 * Returns the column name of the primary key
	 * @return string
	 */
	protected function getPkColumn() {
		return isset($this->fields[$this->primaryKey]['column']) ?
				$this->fields[$this->primaryKey]['column'] :
				$this->primaryKey;
	}

}
