<?php
App::uses('ReportAppModel', 'Report.Model');
/**
 * Report Model
 *
 * @property User $User
 * @property department $department
 * @property ReportItem $ReportItem
 */
class Report extends ReportAppModel {

/**
 * Virtual fields
 *
 * @array
 */
	public $virtualFields = array(
		'label' => 'CONCAT(%s.year, "W", LPAD(%s.week, 2, 0))',
	);

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Multivalidatable.Multivalidatable'
	);

/**
 * Validation domain
 *
 * @var array
 */
	public $validationDomain = 'Report';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'published' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'accepted' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'review' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'department_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'existing' => array(
				'rule' => array('validate_departmendExists'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

/**
 * Validation sets (MultivalidatableBehavior)
 *
 * @var array
 */
	public $validationSets = array(
		'report_add' => array(
			'week' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'range' => array(
					// We need to -1/+1 here, because the min/max of the validation methods are not included
					'rule' => array('range', -1, 54),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'validWeekInYear' => array(
					'rule' => array('validate_weekInYear'),
					'message' => 'There is no such week in the given year.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'weekInThePast' => array(
					'rule' => array('validate_weekInThePast'),
					'message' => 'Please choose the current or a past week.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'weekInUserRange' => array(
					'rule' => array('validate_weekInUserRange'),
					'message' => 'This week is before you began to work here.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'year' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'inUserRange' => array(
					'rule' => array('validate_yearInUserRange'),
					'message' => 'This year is not in your range of years.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			)
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Report.Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ReportItem' => array(
			'className' => 'Report.ReportItem',
			'foreignKey' => 'report_id',
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

/*******
 * --{{ VALIDATION METHODS }} *
 *					***********/

/**
 * Check if the given deprarment exists
 *
 * @param array $check
 * @return boolean
 */
	public function validate_departmendExists($check){
		$check = $check['department_id'];

		$count = $this->Department->find(
			'count',
			array(
				'conditions' => array(
					$this->Department->escapeField() => $check,
				),
				'recursive' => -1
			)
		);

		return ($count !== 0);
	}

/**
 * Check if the given week is present in the given year.
 * This validation only checks if it is a valid week/year combination
 * if week is bigger than 52 to prevent usage of week 53 in a none
 * leap year.
 *
 * @param array $check
 * @return boolean
 */
	public function validate_weekInYear($check){
		$check = current(array_values($check));
		$year = $this->data[$this->alias]['year'];

		return !( $check > 52 && !checkdate(2, 29, $year) );
	}

/**
 * Check if the given week is either current or past one.
 *
 * @param array $check
 * @return boolean
 */
	public function validate_weekInThePast($check){
		$check = current(array_values($check));
		$year = $this->data[$this->alias]['year'];

		return !( $year === DATE_YEAR && $check > DATE_WEEK );
	}

/**
 * Check if the given week is after the users first week.
 *
 * @param array $check
 * @return boolean
 */
	public function validate_weekInUserRange($check, $year = null){
		$check = current(array_values($check));

		$result = false;

		// Get user
		$user = $this->getCurrentUser('User.User');

		if($user){
			// Import needed utilities
			App::uses('CakeTime', 'Utility');

			$year = ($year ? $year : $this->data[$this->alias]['year']);

			$weekOfUser = CakeTime::format(
				$user['User']['entry_date'],
				'%W'
			);

			$yearOfUser = CakeTime::format(
				$user['User']['entry_date'],
				'%Y'
			);
			
			/**
			 * If the given week is not smaller than the first week of the user
			 * and the year does not equal the first year of the user,
			 * the result is true
			 */
			$result = !(
				$check < $weekOfUser &&
				$year === $yearOfUser
			);

		}

		return $result;;
	}

/**
 * Check if the given year is in the range of the users first year and the current.
 *
 * @param array $check
 * @return boolean
 */
	public function validate_yearInUserRange($check){
		$check = current(array_values($check));

		$result = false;

		// Get user
		$user = $this->getCurrentUser('User.User');

		if($user){
			// Import needed utilities
			App::uses('CakeTime', 'Utility');

			$yearOfUser = CakeTime::format(
				$user['User']['entry_date'],
				'%Y'
			);

			$result = (
				// Bigger or equal the first year of the user
				$check >= $yearOfUser &&
				// Lower or equal the current year
				$check <= DATE_YEAR
			);

		}

		return $result;;
	}

/**
 * Find report by week and year and user id (automatically obtained)
 *
 * @param int $week The week number to show
 * @param int $year The year to show
 * @return array
 */
	public function findByWeekAndYear($week, $year){
		$result = array();

		if($week && $year){
			// Get user
			$user = $this->getCurrentUser('User.User');

			if($user){
				// Get reports based on $week and $year and user id
				$options = array(
					'conditions' => array(
						$this->escapeField('week') => $week,
						$this->escapeField('year') => $year,
						$this->escapeField('user_id') => $user['User']['id']
					),
					'recursive' => -1
				);

				$result = $this->find(
					'first',
					$options
				);
			}
		}

		return $result;
	}


/**
 * Validate week and year and correct them if necessary.
 * Returns nothing because it is working with references
 *
 * @param int $week The week number to show
 * @param int $year The year to show
 * @return void
 */
	public function checkWeekAndYear(&$week = null, &$year = null){
		/**
		 * Considering that we out any memory range the year
		 * is only invalid if it not set at all or below 1 (app specific)
		 *
		 * We check year first to prevent checkdate() below
		 * from failing, because of a year below 1.
		 */
		if(
			// No value given for $year
			is_null($year) ||
			$year < 1
		){
			$year = DATE_YEAR;
		}

		if(
			// No value given for $week
			is_null($week) || 
			// $week is out of range (0 - 53)
			$week < 1 || $week > 53 ||
			// $week is bigger than 52 where we have no leap year
			( !checkdate(2, 29, $year) && $week > 52 )
		){
			$week = DATE_WEEK;
		}
	}

/**
 * Check if the given week and year are in the range of the user.
 *
 * @param int $week The week number
 * @param int $year The year
 * @return boolean
 */
	public function weekAndYearInUserRange($week = null, $year = null){
		return (
			!(is_null($week) || is_null($year)) &&
			// If the week is not in the users range
			$this->validate_weekInUserRange(array($week), $year) &&
			// If the year is not in the users range
			$this->validate_yearInUserRange(array($year))
		);
	}

/**
 * Accept report
 *
 * @param int $id Id of the report to accept
 * @return boolean
 */
	public function accept($id){
		$result = false;
		if($this->exists($id)){
			$data = array(
				$this->alias => array(
					$this->primaryKey => $id,
					'accepted' => 1,
					'review' => 0,
				)
			);

			$result = $this->save(
				$data,
				array(
					'validate' => false
				)
			);
		}

		return $result;
	}


/**
 * Publish report
 *
 * @param int $id Id of the report to accept
 * @return boolean
 */
	public function publish($id){
		$result = false;
		if($this->exists($id)){
			$data = array(
				$this->alias => array(
					$this->primaryKey => $id,
					'accepted' => 0,
					'published' => 1,
					'review' => 1,
				)
			);

			$result = $this->save(
				$data,
				array(
					'validate' => false
				)
			);
		}

		return $result;
	}

/**
 * Review report
 *
 * @param int $id Id of the report to accept
 * @return boolean
 */
	public function review($id){
		$result = false;
		if($this->exists($id)){
			$data = array(
				$this->alias => array(
					$this->primaryKey => $id,
					'accepted' => 0,
					'published' => 0,
					'review' => 1,
				)
			);

			$result = $this->save(
				$data,
				array(
					'validate' => false
				)
			);
		}

		return $result;
	}

/**
 * Calculate report number
 *
 * @param string $entryDate The entry date of the user the report belongs to
 * @param string $referenceDate A date to use instead of current
 * @return int
 */
	public function calculateReportNumber($entryDate, $referenceDate = null){
		App::uses('CakeTime', 'Utility');
		
		if(is_null($referenceDate)){
			$referenceDateTs = TIME_NOW;
		}
		else{
			$referenceDateTs = CakeTime::fromString($referenceDate);
		}
		
		$entryDateTs = CakeTime::fromString($entryDate);
		
		// Calculate time difference
		$timeDifference = $referenceDateTs - $entryDateTs;
		
		// Calculate week count
		$result = ceil( $timeDifference / ( 3600 * 24 * 7 ) );
		
		return $result;
	}
}
