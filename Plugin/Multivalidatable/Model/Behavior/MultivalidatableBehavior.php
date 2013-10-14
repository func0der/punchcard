<?php

class MultivalidatableBehavior extends ModelBehavior {

/**
* Stores previous validation ruleset
*
* @var Array
*/
	private $__oldRules = array();

/**
* Stores Model default validation ruleset
*
* @var array
*/
	private $__defaultRules = array();

/**
 * Save default validation rules of current model.
 * @see ModelBehavior::setup()
 */
	public function setup(Model $model, $config = array()) {
		$this->__defaultRules[$model->alias] = $model->validate;
	}

/**
* Installs a new validation ruleset
*
* If $rules is an array, it will be set as current validation ruleset,
* otherwise it will look into Model::validationSets[$rules] for the ruleset to install
*
* @param Model $model
* @param Mixed $rules
* @return void
*/
	public function setValidation(Model $model, $rules = array()) {
		if (is_array($rules)){
			$this->_setValidation($model, $rules);
		} elseif (isset($model->validationSets[$rules])) {
			$this->setValidation($model, $model->validationSets[$rules]);
		}
	}

/**
* Restores previous validation ruleset
*
* @param Model $model
* @return void
*/
	public function restoreValidation(Model $model) {
		$model->validate = $this->__oldRules[$model->alias];
	}

/**
* Restores default validation ruleset
*
* @param Model $model
* @return void
*/
	public function restoreDefaultValidation(&$model) {
		$model->validate = $this->__defaultRules[$model->name];
	}

/**
* Sets a new validation ruleset, saving the previous
*
* @param Model $model
* @param Array $rules
* @return void
*/
	protected function _setValidation(Model $model, $rules) {
		$this->__oldRules[$model->name] = $model->validate;
		$model->validate = $rules;
	}
}

?>
