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
		$model = $this->mapRowToModel($record);
		return $model;
	}

	public function loadByPrimaryKey($pk) {
		return $this->loadOne([
					$this->primaryKey => $pk,
		]);
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
