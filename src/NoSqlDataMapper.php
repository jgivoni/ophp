<?php

namespace Ophp;

/**
 * Standardized mapping between data storage and model
 * 
 * This data mapper should be stateless and not hold any information particular to a
 * specific mapping request.
 * One data mapper per model class is meant to be shared between all models using the 
 * same database adapter
 * 
 * @property \Ophp\dba\DynamoDbDatabaseAdapter $dba
 */
abstract class NoSqlDataMapper extends DataMapper {

	/**
	 * Returns a model for the first row matched
	 * @param \Ophp\SqlQueryBuilder_Select $query
	 * @return Model
	 * @throws \OutOfBoundsException
	 */
	public function loadOne($key) {
		$record = $this->dba->get($this->tableName, $key);
		if (isset($record)) {
			$model = $this->mapRowToModel($record);
		}
		return isset($model) ? $model : null;
	}

	public function loadMany($keys) {
		$records = $this->dba->getBatch($this->tableName, $keys);
		$models = [];
		foreach ($records as $record) {
			$models[] = $this->mapRowToModel($record);
		}
		return $models;
	}

	public function loadByPrimaryKey($pk) {
		return $this->loadOne([
					$this->primaryKey => $pk,
		]);
	}

	public function loadByPrimaryKeys($pks) {
		$keys = [];
		foreach ($pks as $pk) {
			$keys[] = [
				$this->primaryKey => $pk,
			];
		}
		return $this->loadMany($keys);
	}

	protected function modelToArray($model) {
		$arr = [];
		foreach ($this->fields as $modelField => $config) {
			if (isset($model[$modelField])) {
				$name = isset($config['column']) ? $config['column'] : $modelField;
				$arr[$name] = $model->$modelField;
			}
		}
		return $arr;
	}

}
