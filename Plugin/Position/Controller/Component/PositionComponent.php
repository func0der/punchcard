<?php
/**
 * Position Component
 *
 * @author func0der
 */

App::uses('Component', 'Controller');

class PositionComponent extends Component{

/**
 * Instance holder for the main model the controler uses
 *
 * @var Model
 */
	private $__controllerModel;

/**
 * Default settings
 *
 * @var array
 */
	private $__defaultSettings = array(
		'Behavior' => array(
		),
	);

/**
 * Used components
 *
 * @var array
 */
	public $components = array(
		'Session'
	);

/**
 * Construction
 *
 * @param ComponentCollection $collection
 * @param array $settings
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings){
		$settings = array_merge(
			$this->__defaultSettings,
			$settings
		);
		return parent::__construct($collection, $settings);
	}

/**
 * Initialization
 *
 * @param Controller $Controller
 * @return void
 */
	public function initialize(Controller $Controller){
		// Get MVC model
		$this->__controllerModel = $Controller->{$Controller->modelClass};

		// Attached position behavior to MVC model if it has not already been done
		if(!$this->__controllerModel->Behaviors->attached('Position')){
			$this->__controllerModel->Behaviors->load('Position.Position', $this->settings['Behavior']);
		}

		// Check if internal session component usage is needed
		if($Controller->Components->attached('Session')){
			$this->Session = $Controller->Session;
		}
	}

/**
 * Move wrapper
 *
 * @param int $id The primary key value of the entry to move
 * @param string $direction The direction to move to
 * @param array $manipulationConfig see PositionBehavior::manipulatePosition()
 * @return boolean
 */
 	public function move($direction, $id, $manipulationConfig = array()){
 		if(!$this->__controllerModel->exists($id)){
			throw new NotFoundException(__d('position', 'Invalid %s entry provided.',  strtolower($this->__controllerModel->alias)));
		}

 		$direction = strtolower($direction);
 		if(
 			in_array(
 				$direction,
 				array(
 					'up',
 					'down'
 				)
 			)
 		){
 			$methodName = '__move' . ucfirst($direction);
 			if($this->{$methodName}($id, $manipulationConfig)){
 				$this->Session->setFlash(__d('position', '%s successfully moved.', Inflector::humanize($this->__controllerModel->alias)));
 			}
 			else{
 				$this->Session->setFlash(__d('position', 'Moving failed.'));
 			}
 		}
 		else{
 			throw new NotImplementedException(__d('position', 'Invalid moving direction.'));
 		}
 	}

/**
 * Move an item up
 *
 * @param int $id The primary key value of the entry to move
 * @param array $manipulationConfig see PositionBehavior::manipulatePosition()
 * @return boolean
 */
	private function __moveUp($id, $manipulationConfig = array()){
		return $this->__controllerModel->moveUp($id, $manipulationConfig);
	}

/**
 * Move an item down
 *
 * @param int $id The primary key value of the entry to move
 * @param array $manipulationConfig see PositionBehavior::manipulatePosition()
 * @return boolean
 */
	private function __moveDown($id, $manipulationConfig = array()){
		return $this->__controllerModel->moveDown($id, $manipulationConfig);
	}
}