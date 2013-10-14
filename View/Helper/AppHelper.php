<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

/** Stolen from FormHelper (Commit: ed435870ae0b15380b92ba1373983c9e66ebd26c) **/
/**
 * Holds the model references already loaded by this helper
 * product of trying to inspect them out of field names
 *
 * @var array
 */
	protected $_models = array();

/**
 * Guess the location for a model based on its name and tries to create a new instance
 * or get an already created instance of the model
 *
 * @param string $model
 * @return Model model instance
 */
	protected function _getModel($model) {
		$object = null;
		if (!$model || $model === 'Model') {
			return $object;
		}

		if (array_key_exists($model, $this->_models)) {
			return $this->_models[$model];
		}

		if (ClassRegistry::isKeySet($model)) {
			$object = ClassRegistry::getObject($model);
		} elseif (isset($this->request->params['models'][$model])) {
			$plugin = $this->request->params['models'][$model]['plugin'];
			$plugin .= ($plugin) ? '.' : null;
			$object = ClassRegistry::init(array(
				'class' => $plugin . $this->request->params['models'][$model]['className'],
				'alias' => $model
			));
		} elseif (ClassRegistry::isKeySet($this->defaultModel)) {
			$defaultObject = ClassRegistry::getObject($this->defaultModel);
			if ($defaultObject && in_array($model, array_keys($defaultObject->getAssociated()), true) && isset($defaultObject->{$model})) {
				$object = $defaultObject->{$model};
			}
		} else {
			$object = ClassRegistry::init($model, true);
		}

		$this->_models[$model] = $object;
		if (!$object) {
			return null;
		}

		$this->fieldset[$model] = array('fields' => null, 'key' => $object->primaryKey, 'validates' => null);
		return $object;
	}
/** /Stolen from FormHelper **/
}
