<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

/**
 * Active/Inactive constants
 *
 * @const int
 */
	const ACTIVE_INACTIVE = 0;
	const ACTIVE_ACTIVE = 1;
	
/**
 * Save the original recursive depth temporarily
 *
 * @var int $_originalRecursiveDepth
 */
	private $_originalRecursiveDepth;

/**
 * Constructor
 *
 * @params See in Model
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null){
		parent::__construct($id, $table, $ds);
		$this->virtualFields = $this->_insertModelName($this->virtualFields);
	}

/**
 * Replace model name in array values with sprintf. Recursive.
 *
 * @param array $source The array to use for replacement.
 * @param bool $keysToo If true keys are also replaced.
 * @return array The formatted array.
 */
	protected function _insertModelName($source, $keysToo = false){
		$modelName = $this->getName();
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
 * Get the current name of the model in use. This is used in 'AppModel::beforeFind()' to find the right associations
 *
 * @return string
 */
	public function getName(){
		// If there is a an alias given (e.g.: in while combining models over belongsTo and so on) use this or use the normal name of the model
		return isset($this->alias) ? $this->alias : $this->name;
	}

/**
 * Find only active entries of the current model
 *
 * @param string $findType The type of find to use.
 * @param array $queryOptions Additional options to apply to the query.
 * @return array
 */
	public function findActive($findType = 'all', $queryOptions = array()){
		if(!isset($queryOptions['conditions'])){
			$queryOptions['conditions'] = array();
		}
		elseif(is_string($queryOptions['conditions'])){
			$queryOptions['conditions'] = array($queryOptions['conditions']);
		}

		// Prepend active condition to the beginning of the conditions
		array_unshift(
			$queryOptions['conditions'],
			array(
				$this->escapeField('active') => self::ACTIVE_ACTIVE,
			)
		);

		return $this->find(
			$findType,
			$queryOptions
		);
	}

/**
 * Get the complete SQL-Log
 *
 * @return array
 */
	public function getCompleteSqlLog(){
		$sources = $this->useDbConfig;

		// I do not know if the $this->useDbConfig var can be an array. This is just in case it does.
		if(!is_array($sources)){
			$sources = array($sources);
		}

		$logs = array();
		foreach ($sources as $source):
			$db = ConnectionManager::getDataSource($source);
			if (!method_exists($db, 'getLog')):
				continue;
			endif;
			$logs[$source] = $db->getLog();
		endforeach;

		return $logs;
	}

/**
 * Get the SQL-Query-Log
 *
 * @return array
 */
	public function getSqlLog(){
		$log = $this->getCompleteSqlLog();

		$result = array();
		foreach($log as $source => $sourceData){
			$result[$source] = $sourceData['log'];
		}

		return $result;
	}

/**
 * Get the the last SQL-Log
 *
 * @return mixed(array|string)
 */
	public function getLastSqlLog(){
		$logs = $this->getSqlLog();
		$result = array();
		$firstSource = false;
		foreach($logs as $source => $sourceLog){
			if($firstSource === false){
				$firstSource = $source;
			}
			$temp = array_reverse(array_values($sourceLog));
			$result[$source] = $temp[0];
		}

		if(count($result) === 1){
			$result = $result[$firstSource];
		}
		
		return $result;
	}

/**
 * Change the value of a given field
 *
 * @param integer The id of the entry which field should be changed
 * @param string The name of the field which value should be changed
 * @return boolean Result of the saving process
 */
	public function change($id = null, $field = null, $options = array()){
		if(!$id || !$field){
			return false;
		}
		if(empty($options)){
			$options = array(0, 1);
		}

		$record = $this->find(
			'first',
			array(
				'conditions' => array(
					$this->primaryKey => $id,
				),
				'fields' => array(
					$field
				),
				'recursive' => -1,
			)
		);
		$value = (($record[$this->getName()][$field] == $options[0]) ? $options[1] : $options[0]);
		$data = array(
			$this->primaryKey => $id,
			$field => $value
		);
		$this->create();
		return $this->save($data);
	}

/**
 * Set the recursive depth of a model while saving the old value
 *
 * @param int|null $depth New depth to set the recursive property. If "null" set back to saved value.
 * @return void
 */
	public function setRecursive($depth = null){
		// Reset to original depth
		if(is_null($depth) && isset($this->_originalRecursiveDepth)){
			$depth = $this->_originalRecursiveDepth;
			unset($this->_originalRecursiveDepth);
		}
		// We cannot reset the value if we never set another one before.
		elseif(is_null($depth) && !isset($this->_originalRecursiveDepth)){
			$depth = $this->recursive;	
		}
		// Save the original value
		else{
			$this->_originalRecursiveDepth = $this->recursive;
		}
		$this->recursive = $depth;
	}

/**
 * Set the locale of a value
 *
 * @param int|null $locale New locale. If "null" set back to saved value.
 * @return void
 */
	public function setLocale($locale = null){
		// Reset to original depth
		if(is_null($locale) && isset($this->_originalLocale)){
			$locale = $this->_originalLocale;
			unset($this->_originalLocale);
		}
		// We cannot reset the value if we never set another one before.
		elseif(is_null($locale) && !isset($this->_originalLocale)){
			$locale = $this->locale;	
		}
		// Save the original value
		else{
			$this->_originalLocale = $this->locale;
		}
		$this->locale = $locale;
	}

/**
 * General get user.
 *
 * @param string The model to use for fetching data from database.
 * @return array Current user in a model shape
 */
	public static function getCurrentUser($userModel){
		App::uses('AuthComponent', 'Controller/Component');
		if($user = AuthComponent::user()){
			list($plugin, $model) = pluginSplit($userModel);
			if($plugin){
				$plugin = $plugin . '.';
			}
			App::uses($model, $plugin . 'Model');
			$uM = new $model();
			// To keep the data nice and new get it from the database
			$result = $uM->find(
				'first',
				array(
					'conditions' => array(
						$uM->getName() . '.' . $uM->primaryKey => $user[$uM->primaryKey]
					)
				)
			);
			if(!empty($result)){
				if(isset($result[$uM->getName()]['password'])){
					unset($result[$uM->getName()]['password']);
				}
				return $result;
			}
		}
		return false;
	}

/**
 * Generate a new CakeEmail instance and return it.
 *
 * @param $config Config name to extend the CakeEmail Component with.
 * @return CakeEmail An instance of CakeEmail with the given config.
 */
	public function getNewEmail($config = false){
		// Require class file if needed
		if(!class_exists('CakeEmail')){
			App::uses('CakeEmail', 'Network/Email');
		}
		// Initiate a new CakeMail instance with default configuration
		$mail = new CakeEmail('default');
		// If we have a config set extend the used default config with the given one.
		if($config){
			$mail = $mail->config($config);
		}
		return $mail;
	}

/**
 * Sending all emails over this function
 *
 * Works like the following:
 * Every order model has his own mail functions loading the settings for the specific mail, adding recipients and senders. The generated mail CakeEmail object will be given to this function.
 * 
 * @param CakeEmail $mail Email object to use.
 * @param array $params Additional parameters for the email configuration. CURRENTLY: not in use.
 * @return bool Succes or not. Does not indicate if the emails was sent by the server. It only indicates if the mail function ran through properly.
 */
	public function sendEmail($mail, $params = array()){
		$result = false;
		try{
			$result = $mail->from(Configure::read('Administrator.Email'))
			->config(array('additionalParameters' => '-oi -f ' . Configure::read('Administrator.Email')))
			->send();
		}
		catch(Exception $e){
			// If we are in debug mode set transport to debug
			if(Configure::read('debug') > 0){
				$result = $mail->from(Configure::read('Administrator.Email'))
				->readReceipt(Configure::read('Administrator.Email'))
				->config(array('additionalParameters' => '-oi -f ' . Configure::read('Administrator.Email')))
				->transport('debug')
				->send();
			}
		}
		return $result;
	}
}
