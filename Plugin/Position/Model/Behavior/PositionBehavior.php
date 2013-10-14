<?php
/**
 * Position Behavior
 *
 * @author func0der
 */

 App::uses('ModelBehavior', 'Model');

class PositionBehavior extends ModelBehavior{
/**
 * Default settings
 *
 * @var array
 */
	protected $_defaults = array(
		'sortField' => 'position',
		'sortDirection' => 'ASC',
		'orientationField' => '[primaryKey]',
		'orientationDirection' => 'DESC',
		'conditions' => array(),
		'manipulation' => array(
			'amount' => '1',
			'method' => 'lower',
			'allowNegative' => false,
		),
	);

/**
 * Configuration holder
 *
 * @var array
 */
	public $config = array();

/**
 * Save runtime data to prevent data loss
 *
 * @var array
 */
	protected $_runtimeData = array();

/** 
 * Primary key replacement string
 *
 * @var string
 */
	protected $_primaryKeyReplacementString = '[primaryKey]';

/** 
 * Place holder for conditions replacement if it should have the same value as new data
 *
 * @var string
 */
	protected $_asNewDataReplacementString = '[asNewData]';

/**
 * Allowed internal methods for manipulating the position.
 * Note:	If you are using other methods for manipulation than the allowed ones,
 *			it is recommended that your are using your own save method. Otherwise
 *			the PositionBehavior::savePosition() method will just write the 
 *			given position to the database without applying any other updates to other
 *			entries.
 *
 * @var array
 */
	protected $_allowedPositionManipulationMethods = array(
		'lower',
		'raise',
	);


/**
 * Setup
 *
 * @see parent::setup()
 */
	public function setup(Model $Model, $config = array()){
		// Merge with default configuration
		$config = $config + $this->_defaults;

		$config['manipulation'] = $config['manipulation'] + $this->_defaults['manipulation'];

		if(!$Model->hasField($config['sortField'])){
			throw new CakeException(__d('position', 'Model does not have sort field "' . $config['sortField'] . '" in its schema.'));
		}

		if($config['orientationField'] === $this->_primaryKeyReplacementString){
			if(isset($Model->primaryKey) && !empty($Model->primaryKey)){
				$config['orientationField'] = $Model->primaryKey;
			}
			else{
				throw new CakeException(__d('position', 'Primary key replacement was not possible. Missing primary key on model.'));
			}
		}

		if(!$Model->hasField($config['orientationField'])){
			throw new CakeException(__d('position', 'Model does not have orientation field "' . $config['orientationField'] . '" in its schema.'));
		}

		if(
			!in_array($config['manipulation']['method'], $this->_allowedPositionManipulationMethods) &&
			!method_exists($Model, $config['manipulation']['method'])
		){
			throw new NotImplementedException(__d('position', 'Invalid position manipulation method. Please use allowed ones or implement "' . $config['manipulation']['method'] . '" in your ' . $Model->alias . ' model.'));
		}

		// Save configuration
		$this->config[$Model->alias] = $config;

		return parent::setup($Model, $config);
	}

/**
 * Cleanup method
 *
 * @see parent::cleanup()
 */
	public function cleanup(Model $Model){
		unset($this->_runtimeData);
	}

/**
 * beforeValidate callback
 *
 * @see parent::beforeValidate
 */
	public function beforeValidate(Model $Model){
		$this->_setRuntimeData($Model);
		return true;
	}

/**
 * afterSave callback
 *
 * @see parent::afterSave()
 */
	public function afterSave(Model $Model, $created){
		if($created){
			// Save id
			$id = $Model->id;

			$position = $this->findPosition($Model, $Model->data);

			$this->savePosition($Model, $Model->data, $position);

			// Reset model data
			$Model->create($this->_getRuntimeData($Model));
			$Model->id = $id;
		}
		return true;
	}

/**
 * beforeFind callback
 *
 * @see parent::beforeFind()
 */
	public function beforeFind(Model $Model, $query){
		// Get config for current model
		$config = $this->config[$Model->alias];

		if(!isset($query['position']) || $query['position'] === true){
			if(!isset($query['order']) || empty($query['order']) || !isset($query['order'][0]) || $query['order'][0] === false){
				$query['order'] = array(
					$Model->escapeField($config['sortField']) . ' ' . $config['sortDirection'],
				);
			}
		}
		return $query;
	}

/********************************
******** Public Methods *********
*********************************/

/**
 * Wrapper method for parseConditions
 *
 * @param Model $Model
 * @param array $modelData The model data to use as an orientation.
 * @return int
 */
	public function parseConditions(Model $Model, $modelData, $conditions = array()){
		if(method_exists($Model, 'parseConditions')){
			$result = $Model->parseConditions($modelData, $conditions);
		}
		else{
			$result = $this->_parseConditions($Model, $modelData, $conditions);
		}
		return $result;
	}

/**
 * Wrapper method for findPosition
 *
 * @param Model $Model
 * @param array $modelData The model data to use as an orientation.
 * @return int
 */
	public function findPosition(Model $Model, $modelData){
		if(method_exists($Model, 'findPosition')){
			$position = $Model->findPosition($modelData);
		}
		else{
			$position = $this->_findPosition($Model, $modelData);
		}
		return $position;
	}

/**
 * Manipulate the given to position to provide a new and free one.
 *
 * @param Model $Model
 * @param array $lastRecord	The record of the currently last entry in the database.
 * 							It has to at least include the $config['sortField'] index.
 * @param array $manipulationConfig If not set get from saved ones.
 * @return int New position
 */
	public function manipulatePosition(Model $Model, $lastRecord, $manipulationConfig = null){
		// Get config
		$config = $this->config[$Model->alias];

		if(is_null($manipulationConfig)){
			$manipulationConfig = $config['manipulation'];
		}

		if(is_null($lastRecord) || !array_key_exists($config['sortField'], $lastRecord[$Model->alias])){
			throw new Exception(__d('position', 'Sort field field index is not given.'));
		}

		// Extract method
		$manipulationMethod = $manipulationConfig['method'];
		unset($manipulationConfig['method']);

		if(method_exists($Model, $manipulationMethod)){
			return $Model->{$manipulationMethod}($lastRecord[$Model->alias][$config['sortField']], $manipulationConfig);
		}
		else{
			return $this->{$manipulationMethod}($Model, $lastRecord[$Model->alias][$config['sortField']], $manipulationConfig);
		}
	}

/**
 * Wrapper method for savePosition
 *
 * @param Model $Model
 * @param array $modelData Model data
 * @param int $position The position to save
 * @return boolean
 */
	public function savePosition(Model $Model, $modelData, $position){
		if(method_exists($Model, 'savePosition')){
			return $Model->savePosition($modelData, $position);
		}
		else{
			return $this->_savePosition($Model, $modelData, $position);
		}
	}

/**
 * Raise the position by a specific amount
 *
 * @param Model $Model
 * @param int $position The position to process
 * @param array $manipulationConfig The config to use for the manipulation.
 * @return int
 */
	public function raise(Model $Model, $position, $manipulationConfig){
		return $position + $manipulationConfig['amount'];
	}

/**
 * Lower the position by a specific amount
 *
 * @param Model $Model
 * @param int $position The position to process
 * @param array $manipulationConfig The config to use for the manipulation.
 * @return int
 */
	public function lower(Model $Model, $position, $manipulationConfig){
		$newPosition = $position - $manipulationConfig['amount'];

		if($newPosition <= 0 && $manipulationConfig['allowNegative'] !== true){
			$newPosition = 0;
		}

		return $newPosition;
	}


/**
 * Move up
 *
 * @param Model $Model
 * @param int $id The primary key value of the entry to move.
 * @param array $manipulationConfig Additional configuration to be applied to the default.
 * @return boolean
 */
	public function moveUp(Model $Model, $id, $manipulationConfig = array()){
		// Get config
		$config = $this->config[$Model->alias];

		$manipulationConfig = array_merge(
			// Get default configuration
			$config['manipulation'],
			// Set method to lower and amount to 1
			array(
				'method' => 'lower',
				'amount' => 1
			),
			// Merge with given manipulation configuration
			$manipulationConfig
		);

		$record = $Model->find(
			'first',
			array(
				'conditions' => array(
					$Model->escapeField() => $id,
				),
				'fields' => array(
					$Model->escapeField(),
					$Model->escapeField($config['sortField']),
				),
			)
		);

		$newPosition = $this->manipulatePosition($Model, $record, $manipulationConfig);

		return $this->savePosition($Model, $record, $newPosition);
	}


/**
 * Move down
 *
 * @param Model $Model
 * @param int $id The primary key value of the entry to move.
 * @param array $manipulationConfig Additional configuration to be applied to the default.
 * @return boolean
 */
	public function moveDown(Model $Model, $id, $manipulationConfig = array()){
		// Get config
		$config = $this->config[$Model->alias];

		$manipulationConfig = array_merge(
			// Get default configuration
			$config['manipulation'],
			// Set method to lower and amount to 1
			array(
				'method' => 'raise',
				'amount' => 1
			),
			// Merge with given manipulation configuration
			$manipulationConfig
		);

		$record = $Model->find(
			'first',
			array(
				'conditions' => array(
					$Model->escapeField() => $id,
				),
				'fields' => array(
					$Model->escapeField(),
					$Model->escapeField($config['sortField']),
				),
			)
		);

		$newPosition = $this->manipulatePosition($Model, $record, $manipulationConfig);

		return $this->savePosition($Model, $record, $newPosition);
	}


/********************************
******** Private Methods ********
*********************************/

/**
 * Method to parse the given conditions.
 *
 * @param Model $Model
 * @param array $modelData The model data to use as an orientation.
 * @param array $conditions The conditions to parse.
 * @return boolean
 */
	public function _parseConditions(Model $Model, $modelData, $conditions = array()){
		if(is_array($conditions) && !empty($conditions)){
			// Replace missing model names
			$conditions = $this->_insertModelName($Model, $conditions, true);

			// Replace asNewData
			$conditions = $this->_replaceAsNewDataValues($Model, $modelData, $conditions);
		}
		return $conditions;
	}

/**
 * Method to find available position and save it.
 *
 * @param Model $Model
 * @param array $modelData The model data to use as an orientation.
 * @return boolean
 */
	public function _findPosition(Model $Model, $modelData){
		// Get config for current model
		$config = $this->config[$Model->alias];

		$conditions = array_merge(
			$this->parseConditions($Model, $modelData, $config['conditions']),
			array(
				$Model->escapeField() . ' !=' => $modelData[$Model->alias][$Model->primaryKey],
			)
		);

		$lastRecord = $Model->find(
			'first',
			array(
				'conditions' => $conditions,
				'fields' => array(
					$Model->escapeField(),
					$Model->escapeField($config['sortField']),
				),
				'order' => array(
					/**
					 * @TODO: Check this:
					 *		If we sort this after the orientation field all the time and raise
					 *		but not lower the position in a manually modified sorting, it may fail the existing positions, because it
					 *		will use the highest value of the orientation field which is not always the highest position
					 */
					$Model->escapeField($config['orientationField']) . ' ' . $config['orientationDirection']
				),
			)
		);

		// If this saving is the first entry ever we set the sortfield to 0
		if(!$lastRecord){
			$lastRecord = array(
				$Model->alias => array(
					$config['sortField'] => 0,
				),
			);
		}

		// Get new position for the record by manipulating the last position
		return $this->manipulatePosition($Model, $lastRecord);
	}

/**
 * Save the position
 *
 * @param Model $Model
 * @param array $modelData Model data
 * @param int $position The position to save
 * @return boolean
 */
	public function _savePosition(Model $Model, $modelData, $position){
		// Get config
		$config = $this->config[$Model->alias];

		// Indicator if the position was already tried to save
		$positionSaveAttempt = false;

		$id = $modelData[$Model->alias][$Model->primaryKey];

		// Check if position is already taken
		// @TODO: Check for the best method here
		/*$lastRecord = $Model->find(
			'first',
			array(
				'conditions' => array(
					$Model->escapeField() . ' !=' => $modelData[$Model->alias][$Model->primaryKey],
					$Model->escapeField($config['sortField']) => $position,
				),
				'fields' => array(
					$Model->escapeField(),
					$Model->escapeField($config['sortField']),
				),
			)
		);

		// If we have an entry with the same position we need to raise it and all others
		if($lastRecord){
			$records = $Model->find(
				'all',
				array(
					'conditions' => array(
						$Model->escapeField() . ' !=' => $modelData[$Model->alias][$Model->primaryKey],
						$Model->escapeField($config['sortField']) . ' >=' => $position,
					),
					'fields' => array(
						$Model->escapeField(),
						$Model->escapeField($config['sortField']),
					),
				)
			);

			foreach($records as $record){
				$record[$Model->alias][$config['sortField']]++;
				$Model->create($record);

				$Model->saveField(
					$config['sortField'],
					$record[$Model->alias][$config['sortField']],
					array(
						'callbacks' => false,
						'validate' => false
					)
				);
			}
		}

		$Model->create($modelData);

		// No callbacks and no validation to prevent other behaviors from failing
		$result = !(
			$Model->saveField(
				$config['sortField'],
				$position,
				array(
					'callbacks' => false,
					'validate' => false
				)
			) === false
		);*/

		$conditions = array_merge(
			$this->parseConditions($Model, $modelData, $config['conditions']),
			array(
				$Model->escapeField() . ' !=' => $id,
			)
		);

		// Kind of dirty method so set all positions news
		$entries = $Model->find(
			'all',
			array(
				'conditions' => $conditions,
				'fields' => array(
					$Model->escapeField(),
				),
				'order' => array(
					$Model->escapeField($config['sortField']) . ' ASC',
				)
			)
		);

		$i = 0;

		foreach($entries as $entry){
			// If $i equals the new position we save the entry before actually continuing with the loop
			if($i === $position){
				$Model->create($modelData);
				$result = $Model->saveField(
					$config['sortField'],
					$i,
					array(
						'callbacks' => false,
						'validate' => false,
					)
				);
				$positionSaveAttempt = true;
				$i++;
			}

			$Model->create($entry);
			$Model->saveField(
				$config['sortField'],
				$i,
				array(
					'callbacks' => false,
					'validate' => false,
				)
			);
			$i++;
		}

		// If we had no attempt to save the new position during the loop do it now
		if(!$positionSaveAttempt){
			$Model->create($modelData);
			$result = $Model->saveField(
				$config['sortField'],
				$i,
				array(
					'callbacks' => false,
					'validate' => false,
				)
			);
		}

		return !($result === false);
	}

/**
 * Set runtime data
 *
 * @param Model $Model
 * @param array $index The index to save the data in.
 * @return void
 */
	private function _setRuntimeData(Model $Model, $index = 'beforeSave'){
		$this->_runtimeData[$Model->alias][$index] = $Model->data;
	}

/**
 * Get runtime data
 *
 * @param Model $Model
 * @param array $index The index to save the data in.
 * @return mixed[null|array]
 */
	private function _getRuntimeData(Model $Model, $index = 'beforeSave'){
		if(array_key_exists($index, $this->_runtimeData[$Model->alias])){
			return $this->_runtimeData[$Model->alias][$index];
		}
		return null;
	}

/**
 * Replace model name in array values with sprintf. Recursive.
 *
 * @param Model $Model
 * @param array $source The array to use for replacement.
 * @param bool $keysToo If true keys are also replaced.
 * @return array The formatted array.
 */
	private function _insertModelName(Model $Model, $source, $keysToo = false){
		$modelName = $Model->alias;
		foreach($source as $field => $value){
			if($keysToo){
				$oldField = $field;
				$count = substr_count($field, '%s');
				if($count !== false){
					$temp = array();
					$temp[] = $field;
					for($i = 0; $i < $count; $i++){
						$temp[] = $modelName;
					}
					$field = call_user_func_array('sprintf', $temp);
				}
				if($field != $oldField){
					unset($source[$oldField]);
				}
			}
			if(is_array($value)){
				$source[$field] = $this->_insertModelName($value, $keysToo);
			}
			else{
				$count = substr_count($value, '%s');
				if($count !== false){
					$temp = array();
					$temp[] = $value;
					for($i = 0; $i < $count; $i++){
						$temp[] = $modelName;
					}
					$source[$field] = call_user_func_array('sprintf', $temp);
				}
			}
		}
		return $source;
	}

/**
 * Replace values where needed with current model data. NOT Recursive.
 *
 * @param Model $Model
 * @param array $modelData
 * @param array $source The source to replace in
 * @return array
 */
	private function _replaceAsNewDataValues(Model $Model, $modelData, $source){
		App::uses('Set', 'Utility');

		foreach($source as $index => $value){
			if(substr_count($index, $Model->alias) !== false){
				if(is_array($value)){
					foreach($value as $i => $v){
						if(substr_count($v, $this->_asNewDataReplacementString) !== false){
							$value[$i] = str_replace($this->_asNewDataReplacementString, Set::extract($index, $modelData), $v);
						}
					}
				}
				else{
					$source[$index] = str_replace($this->_asNewDataReplacementString, Set::extract($index, $modelData), $value);
				}
			}
		}

		return $source;
	}
}