<?php
/**
 * Select2 Helper
 *
 * @author func0der
 */

	App::uses('AppHelper', 'View/Helper');

/**
 * Select2 helper
 */
class Select2Helper extends AppHelper{

/**
 * Used helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'Js',
	);

/**
 * Scripts included indicator
 *
 * @var boolean
 */
	private $__scriptsIncluded = false;

/**
 * Constructor
 *
 * @param View $View
 * @param array $settings
 * @return void
 */
	public function __construct(View $View, $settings = array()){
		// If minify is not forced set it to false in debug
		if(!isset($settings['minify'])){
			if(Configure::read('debug') >= 0){
				$settings['minify'] = false;
			}
		}
		return parent::__construct($View, $settings);
	}

/**
 * Include javascript and css
 *
 * @return void
 */
	public function includeScripts(){
		// Include scripts only once
		if($this->__scriptsIncluded){
			return;
		}

		$js = 'Select2.select2';
		if($this->settings['minify']){
			$js .= '.min';
		}

		$this->Html->script(
			$js,
			array(
				'inline' => false,
			)
		);

		$this->Html->css(
			'Select2.select2',
			array(
				'inline' => false,
			)
		);

		$this->__scriptsIncluded = true;
	}

/**
 * Include js needed for converting normal select to select2 element.
 *
 * @param mixed[string|array] $selectors The selector with the config to use (depends on the given var type)
 * @param array $scriptBlockOptions Options to apply on the scriptblock
 * @return void
 */
	public function select2($selectors, $scriptBlockOptions = array()){
		if(!$selectors){
			return;
		}

		// Include basic scripts
		$this->includeScripts();

		if(!is_array($selectors)){
			$selectors = array($selectors);
		}

		$scriptBlockOptions += array(
			'inline' => false
		);

		$scriptBlock = '
			jQuery(document).ready(function($){' . "\n";

		foreach($selectors as $selector => $config){
			if(is_array($config)){
				$scriptBlock .= $this->__generateSelect2($selector, $config);
			}
			else{
				$scriptBlock .= $this->__generateSelect2($config);
			}
			$scriptBlock .= "\n";
		}

		$scriptBlock .= '});';

		$this->Html->scriptBlock(
			$scriptBlock,
			$scriptBlockOptions
		);
	}

/**
* Generate select2 javascript
*
* @param string $selector The selector to use in javascript
* @param array $config The configuration to use for the selector
* @return string
*/
	private function __generateSelect2($selector, $config = array()){
		$result = '$("' . $selector . '").select2(';
		$result .= $this->Js->object($config);
		$result .= ');';

		return $result;
	}
}

?>