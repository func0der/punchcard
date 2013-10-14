<?php
App::uses('UserAppModel', 'User.Model');
/**
 * User Model
 *
 * @property Department $Department
 * @property User $ParentUser
 * @property Report $Report
 * @property User $ChildUser
 */
class User extends UserAppModel {

/**
 * Virtual fields
 *
 * @array
 */
	public $virtualFields = array(
		'label' => 'CONCAT(%s.forename, " ", %s.surname)',
	);

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
		'Multivalidatable.Multivalidatable',
		'Tree'
	);

/**
 * Validation domain
 *
 * @var array
 */
	public $validationDomain = 'User';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('validate_emailIsUnique', true),
				'message' => 'A user with this email address is already registered.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'forename' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'surname' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'entry_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_instructor' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'active' => array(
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
		),
		'parent_id' => array(
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

/**
 * Validation sets
 *
 * @var array
 */
	public $validationSets = array(
		'user_login' => array(
			'email' => array(
				'email' => array(
					'rule' => array('notempty'),
					'message' => 'Please provide a valid username.',
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'inactive' => array(
					'rule' => array('validation_activeUser'),
					'message' => 'This account is deactivated.',
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				)
			),
			'password' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Please provide a password.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				)
			)
		),
		'instructor_edit' => array(
			'email' => array(
				'email' => array(
					'rule' => array('email'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'unique' => array(
					'rule' => array('validate_emailIsUnique'),
					'message' => 'A user with this email address is already registered.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'forename' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'surname' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'entry_date' => array(
				'date' => array(
					'rule' => array('date'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'active' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		),
		'instructor_change_password' => array(
			'new_password' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Please provide a password.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'new_password_confirm' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Please provide a password.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'sameAsNew' => array(
					'rule' => array('validation_passwordConfirmation'),
					'message' => 'The password confirmation does not match the new password.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				)
			)
		),
		'instructor_profile' => array(
			'email' => array(
				'email' => array(
					'rule' => array('email'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'unique' => array(
					'rule' => array('validate_emailIsUnique'),
					'message' => 'A user with this email address is already registered.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'forename' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'surname' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'signature_cert' => array(
				'file' => array(
					'rule' => array('_validation_isUploadedFile'),
					'message' => 'Please provide a valid file',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'valid_certificate' => array(
					'rule' => array('_validation_hasValidCertificate'),
					'message' => 'This file does not contain a certificate.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'valid_private_key' => array(
					'rule' => array('_validation_hasValidPrivateKey'),
					'message' => 'This file does not contain a private key.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'signature_image' => array(
				'file' => array(
					'rule' => array('_validation_isUploadedFile'),
					'message' => 'Please provide a valid file',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'isGif' => array(
					'rule' => array('_validation_isGifImage'),
					'message' => 'Please provide a gif image.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		),
		'instructor_profile_withoutUpload' => array(
			'email' => array(
				'email' => array(
					'rule' => array('email'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'unique' => array(
					'rule' => array('validate_emailIsUnique'),
					'message' => 'A user with this email address is already registered.',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'forename' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'surname' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
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
		'Department' => array(
			'className' => 'Report.Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentUser' => array(
			'className' => 'User.User',
			'foreignKey' => 'parent_id',
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
		'Report' => array(
			'className' => 'Report.Report',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ChildUser' => array(
			'className' => 'User.User',
			'foreignKey' => 'parent_id',
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
 * Checks if the user has been set to inactive
 * 
 * @param mixed $check Email
 * @return bool Wheter the user with the given email is active (true) or not (false).
 */
	public function validation_activeUser($check){		
		$check = array_shift($check);

		$count = $this->find(
			'count',
			array(
				'conditions' => array(
					$this->getName() . '.email' => $check,
					$this->getName() . '.active' => 1
				),
				'recursive' => -1
			)
		);
		return ($count !== 0);
	}

/**
 * Checks if the given email address is already present in the database.
 *
 * @param array $check
 * @return boolean
 */
	public function validate_emailIsUnique($check, $create = false){
		$check = current(array_values($check));

		$conditions = array(
			$this->escapeField('email') => $check,
		);

		if($create){
			$conditions[$this->escapeField() . ' !='] = $this->data['User']['id'];
		}

		$count = $this->find(
			'count',
			array(
				'conditions' => $conditions,
				'recursive' => -1,
			)
		);

		return ($count === 0);
	}

/**
 * Checks if the given password matches the new password in the model data
 *
 * @param mixed $check Password confirmation
 * @return bool Whether the confirmation password matches the new one(true) or not(false).
 */
	public function validation_passwordConfirmation($check){
		$check = current(array_values($check));
		return ($check == $this->data[$this->alias]['new_password']);
	}

/**
 * Validation method for file uploads
 *
 * @param array $check The value to check
 * @return bool Valid or not
 */
	public function _validation_isUploadedFile($check) {
		$val = array_shift($check);
		if (
			(isset($val['error']) && $val['error'] == 0) ||
			(!empty( $val['tmp_name']) && $val['tmp_name'] != 'none')
		) {
			return is_uploaded_file($val['tmp_name']);
		}
		return false;
	}

/**
 * Check if the uploaded file contains a certificate.
 * This is very cheap. It just checks for '-----BEGIN CERTIFICATE-----' and
 * '-----END CERTIFICATE-----' in the given file.
 *
 * @param array $check The value to check
 * @return bool Valid or not
 */
	public function _validation_hasValidCertificate($check) {
		$val = array_shift($check);

		$begin = '-----BEGIN CERTIFICATE-----';
		$end = '-----END CERTIFICATE-----';

		$fileContent = file_get_contents($val['tmp_name']);

		return (
			strpos($fileContent, $begin) !== false &&
			strpos($fileContent, $end) !== false
		);
	}

/**
 * Check if the uploaded file contains a certificate.
 * This is very cheap. It just checks for '-----BEGIN PRIVATE KEY-----' and
 * '-----END PRIVATE KEY-----' in the given file.
 *
 * @param array $check The value to check
 * @return bool Valid or not
 */
	public function _validation_hasValidPrivateKey($check) {
		$val = array_shift($check);

		$begin = '-----BEGIN PRIVATE KEY-----';
		$end = '-----END PRIVATE KEY-----';

		$fileContent = file_get_contents($val['tmp_name']);

		return (
			strpos($fileContent, $begin) !== false &&
			strpos($fileContent, $end) !== false
		);
	}

/**
 * Check if the uploaded file is a gif image.
 *
 * @param array $check The value to check
 * @return bool Valid or not
 */
	public function _validation_isGifImage($check) {
		$val = array_shift($check);

		App::uses('File', 'Utility');

		$file = new File($val['tmp_name']);

		list(, $ext) = explode('/', $file->mime());

		return ($ext === 'gif');
	}

/**
 * afterSave
 *
 * Doing here:
 * 	PASSWORD: Hashes the password
 *
 */
	public function afterSave($created){
		if($created){
			// PASSWORD
			$password = $this->hashPassword($this->data[$this->getName()]['password']);

			$this->saveField('password', $password);
		}
		return parent::afterSave($created);
	}

/**
 * Hashing method for customers passwords
 *
 * @param string $password The clear password.
 * @return string The hashed password.
 */
	public static function hashPassword($password){
		App::uses('AuthComponent', 'Controller/Component');
		return AuthComponent::password($password);
	}

/**
 * Check if the current logged in instructor is parent
 * of the given user.
 *
 * @param int $userId
 * @return boolean
 */
	public function userIsChild($id){
		// Get current user
		$user = $this->getCurrentUser('User.User');

		$conditions = array(
			$this->escapeField() => $id,
			$this->escapeField('parent_id') => $user['User']['id'],
		);

		$count = $this->find(
			'count',
			array(
				'conditions' => $conditions,
				'recursive' => -1
			)
		);

		return ($count === 1);
	}

/**
 * Calculate apprenticeship year
 *
 * @param string $entry_date The entry date
 * @param string $referenceDate A date to use instead of current
 * @return int
 */
	public function calculateApprenticeshipYear($entry_date, $referenceDate = null){
		App::uses('CakeTime', 'Utility');
		
		if(is_null($referenceDate)){
			$referenceDateTs = TIME_NOW;
		}
		else{
			$referenceDateTs = CakeTime::fromString($referenceDate);
		}
		
		$entryDateTs = CakeTime::fromString($entry_date);
		
		$i = 0;
		while($referenceDateTs > $entryDateTs){
			$i++;
			
			$entryDateTs = CakeTime::fromString(
				$entry_date . ' + ' . $i . ' year'
			);
			if($i == 4) break;
		}

		return $i;
	}

/**
 * Saves signature data
 *
 * @param int $id The id of user to save data for.
 * @param array $data The request data of a post call.
 * @return bool
 */
	public function saveSignatureData($id, $data){
		$result = false;

		if(
			isset($data['User']['signature_cert']) && 
			isset($data['User']['signature_image'])
		){
			$basePath = Configure::read('Report.PDF.SignaturePath') . 'user_' . $id;

			$result = (
				move_uploaded_file(
					$data['User']['signature_cert']['tmp_name'],
					$basePath . '.crt'
				) &&
				move_uploaded_file(
					$data['User']['signature_image']['tmp_name'],
					$basePath . '.gif'
				)
			);

		}

		return $result;
	}

/**
 * Get users signature data if he is an instructor.
 *
 * @param int $id The id of the user
 * @return array
 */
	public function getSignatureData($id){
		$result = array();
		if($this->exists($id)){
			$user = $this->find(
				'first',
				array(
					'conditions' => array(
						$this->escapeField() => $id,
						$this->escapeField('is_instructor') => 1,
					),
					'fields' => array(
						$this->escapeField(),
					),
					'recursive' => -1,
				)
			);

			if($user){
				$userSignatureBase = Configure::read('Report.PDF.SignaturePath') . 'user_' . $user['User']['id'];
				$cert = $userSignatureBase . '.crt';
				$image = $userSignatureBase . '.gif';

				if(
					file_exists($cert) && file_exists($image)
				){
					$result = array(
						'cert' => $cert,
						'image' => $image
					);
				}
			}
		}
		return $result;
	}
}
