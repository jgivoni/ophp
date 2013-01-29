<?php
abstract class DataMapper {
	/**
	 * @var SqlDatabaseAdapterInterface
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
	abstract protected function newModel();
	
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

	/**
	 * 
	 * @param mixed $pk
	 * @return Model
	 * @throws Exception
	 */
	public function loadByPrimaryKey($pk) {
		$recordSet = $this->dba
				->select($this->fields)
				->from("`{$this->tableName}`")
				->where('`'.$this->fields[$this->primaryKey].'` = '.(int)$pk)
				->run();
				
		if ($recordSet->isEmpty()) {
			throw new OutOfBoundsException('Row not found');
		}
		
		return $this->mapRowToModel($recordSet->first());
	}
	
	/**
	 * 
	 * @return array Of Model
	 */
	public function loadAll() {
		$recordSet = $this->dba
				->select($this->fields)
				->from("`{$this->tableName}`")
				->orderBy('position')
				->run();
				
		$models = array();
		foreach ($recordSet as $record) {
			$model = $this->mapRowToModel($record);
			$this->setSharedModel($model);
			$models[] = $model;
		}
		return $models;
	}
	
	/**
	 * 
	 * @param array $row
	 * @return Model
	 */
	protected function mapRowToModel($row) {
		$model = $this->newModel();
		foreach ($this->fields as $key => $name) {
			$modelField = is_numeric($key) ? $name : $key;
			$model[$modelField] = $row[$name];
		}
		return $model;
	}

}