<?php
App::uses('ReportAppModel', 'Report.Model');
/**
 * Activity Model
 *
 * @property ReportItem $ReportItem
 */
class Activity extends ReportAppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'label';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Position.Position' => array(
			'sortDirection' => 'DESC',
			'conditions' => array(
				'%s.report_item_id' => '[asNewData]'
			),
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'report_item_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'label' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'duration' => array(
			'time' => array(
				'rule' => array('validation_time'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'position' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ReportItem' => array(
			'className' => 'Report.ReportItem',
			'foreignKey' => 'report_item_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/******************************
 * --{{ VALIDATION METHODS }}--
 *****************************/
	
	public function validation_time($check){
		$check = current(array_values($check));

		return ReportSupporter::validation_time($check);
	}

/**
 * beforeSave callback
 * Tasks:
 *	- MODIFYDURATION: The duration needs to be converted to a decimal representation.
 *
 * @param array $queryData The options the save call was made with.
 * @return array
 */
	public function beforeSave($queryData = array()){
		// MODIFYDURATION
		$this->data[$this->alias]['duration'] = $this->timeToDecimal($this->data[$this->alias]['duration']);

		return $queryData;
	}

/**
 * afterFind callback
 * Tasks:
 *	- MODIFYDURATION: The duration needs to be converted to a time.
 *
 * @param array $results The results
 * @param boolean $primary Was the model called directly or via association.
 * @return array
 */
	public function afterFind($results = array(), $primary = false){
		// MODIFYDURATION
		foreach($results as $index => $result){
			if(isset($results[$index][$this->alias]) && isset($results[$index][$this->alias]['duration'])){
				$results[$index][$this->alias]['duration'] = $this->decimalToTime($result[$this->alias]['duration']);
			}
		}

		return $results;
	}

/**
 * Convert time (hh:mm) to sql decimal representation.
 * Wrapper for ReportSupporter::timeToDecimal()
 * Deprecated.
 *
 * @param string $time
 * @return decimal
 */
	public function timeToDecimal($time){
		return ReportSupporter::timeToDecimal($time);
	}

/**
 * Convert sql decimal to time (hh:mm) representation
 * Wrapper for ReportSupporter::decimalToTime()
 * Deprecated.
 *
 * @param string $time
 * @return decimal
 */
	public function decimalToTime($decimal){
		return ReportSupporter::decimalToTime($decimal);
	}
}
