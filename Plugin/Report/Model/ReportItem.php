<?php
App::uses('ReportAppModel', 'Report.Model');
/**
 * ReportItem Model
 *
 * @property Report $Report
 * @property Activity $Activity
 */
class ReportItem extends ReportAppModel {

/**
 * Default order
 *
 * @var array
 */
	public $order = array(
		'day' => 'DESC',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'duration' => array(
			'time' => array(
				'rule' => array('time'),
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
		'Report' => array(
			'className' => 'Report.Report',
			'foreignKey' => 'report_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'Comment' => array(
			'className' => 'Report.Comment',
			'foreignKey' => 'report_item_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => '',
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Activity' => array(
			'className' => 'Report.Activity',
			'foreignKey' => 'report_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * afterFind
 * Tasks:
 *	- ACTIVITYGETTING: Get activities automatically, because Behaviors does not apply on associated models.
 *
 * @param array $results The results
 * @param boolean $primary Was the model called directly or via association.
 * @return array
 */
	public function afterFind($results = array(), $primary = true){
		// ACTIVITYGETTING
		if(!empty($results) && isset($results[0]['Activity'])){
			// Include Set class
			App::uses('Set', 'Utility');

			foreach($results as $index => $result){
				$results[$index]['Activity'] = Set::sort(
					$result['Activity'],
					'{n}.position',
					$this->Activity->Behaviors->Position->config[$this->Activity->alias]['sortDirection']
				);
			}
		}
		return $results;
	}

/**
 * Find the date for the given day and the given report which contains
 * week and year.
 *
 * @param int $day The day of the week
 * @param int $reportId A report id
 * @return string A sql date formatted string
 */
	public function findDateByDayAndReport($day, $reportId){
		if(
			$day >= 1 && $day <= 7 &&
			$this->Report->exists($reportId)
		){
			$report = $this->Report->find(
				'first',
				array(
					'conditions' => array(
						$this->Report->escapeField() => $reportId,
					),
					'fields' => array(
						'week',
						'year'
					),
					'recursive' => -1
				)
			);
			$report['Report']['week'] = ($report['Report']['week'] < 10) ? '0' . $report['Report']['week'] : $report['Report']['week'];

			$toTimeString = $report['Report']['year'] . 'W' . $report['Report']['week'] . '-' . $day;

			return strftime(SQL_DATE_FORMAT, strtotime($toTimeString));
		}

		return null;
	}

/**
 * Get the report item for the given data. If none exists create one.
 *
 * @param
 * @return mixed[array|false] The requested report item or false if the creation failed
 */
	public function getReportItem($data){
		$result = array();
		$createReportItem = false;

		if(isset($data['ReportItem']['id']) && !empty($data['ReportItem']['id'])){
			if(!$this->exists($data['ReportItem']['id'])){
				$createReportItem = true;
			}
			else{
				$result = $this->find(
					'first',
					array(
						'conditions' => array(
							$this->escapeField() => $data['ReportItem']['id'],
						),
						'recursive' => -1
					)
				);
			}
		}
		else{
			$createReportItem = true;
		}

		if($createReportItem){
			// Transform data due to FormHelper bug
			$data['ReportItem']['day'] = $data['ReportItem']['dayOfTheWeek'];
			unset($data['ReportItem']['dayOfTheWeek']);

			// Get the date for the date for the given week day and report
			$data['ReportItem']['date'] = $this->findDateByDayAndReport($data['ReportItem']['day'], $data['ReportItem']['report_id']);

			$result = $this->save(
				$data,
				array(
					'fieldList' => array(
						$this->alias => array(
							'day',
							'date',
							'report_id',
						),
					)
				)
			);
		}

		return $result;
	}

/**
 * Update the duration of the given report item
 *
 * @param int $id The id of the report item to update
 * @return boolean
 */
	public function updateTotalDuration($id){
		$result = false;

		if($result = $this->exists($id)){
			$activities = $this->Activity->find(
				'all',
				array(
					'conditions' => array(
						$this->Activity->escapeField('report_item_id') => $id,
					),
					'fields' => array(
						$this->Activity->escapeField('duration')
					)
				)
			);

			$totalDuration = 0;

			foreach($activities as $activity){
				$totalDuration += $this->Activity->timeToDecimal($activity['Activity']['duration']);
			}

			$this->id = $id;

			$result = $this->saveField('duration', $totalDuration);
		}

		return $result;
	}
}
