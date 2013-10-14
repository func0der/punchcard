<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
/**
 * Components
 *
 * @var array
 */
	public $components = array(
		// BB magic
		'Auth' => array(
			'authorize' => array(
				'Controller' => array(
				)
			)
		),
		'BB.BbCore',
		'Cookie',
		// 'DebugKit.Toolbar',
		'Session',
		'Security' => array(
			'blackHoleCallback' => 'blackHole',
			'csrfUseOnce' => false,
			'csrfExpires' => '+30 minutes'
		),
		'Twb.TwbCore'
	);

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Form' => array(
			'className' => 'Twb.TwbForm'
		),
		'Html',
		'Session',
		'Time'
	);

/**
 * Layout
 *
 * @var string
 */
	public $layout = 'responsive_default';

/**
 * Used models
 *
 * @var array
 */
	public $uses = array(
		'User.User',
	);

/**
 * As we need the current auth model in the general AppModel::getUser() method 
 * we need to save it's name in the controller to use it later on.
 *
 * @var string
 */
	protected $_authComponentUserModel = null;

/**************************************
 ********** Callback methods ***********
 **************************************/

/**
 * beforeFilter callback
 * Tasks:
 *	- COOKIEENCRYPTION: Set cookie encryption to work on suhosin servers
 *	- AUTHCOMP: Defines parameters for the authorize component
 *	- AUTHCOMPPERMISSIONS: Calls permission setter function
 *	- LANGUAGE: Language setter wrapper.
 *
 * @return void
 */
	public function beforeFilter(){
		//AUTHCOMP
		$customAuth = false;

		// COOKIEENCRYPTION
		$this->Cookie->type('rijndael');
		
		// ADMINLAYOUT
		// Setting it here to make it possible to overwrite it in the action.
		if($this->isAdminArea()){
		}

		// AUTHCOMP
		$customAuth = true;
		$userPlugin = 'User';
		$userModel = 'User';
		$loginRedirect = array(
			'plugin' => 'user',
			'controller' => 'users',
			'action' => 'redirect_by_role',
			'admin' => false,
			'instructor' => false,
		);
		$loginAction = array(
			'plugin' => 'user',
			'controller' => 'users',
			'action' => 'login',
			'admin' => false,
			'instructor' => false,
		);
		$logoutRedirect = $loginAction;
		$sessionKey = 'Auth.User';
		$fields = array(
			'username' => 'email'
		);

		if($customAuth){
			// AUTHCOMP
			$userPlugin = isset($userPlugin) ? $userPlugin . '.' : null;
			$userModel = isset($userModel) ? $userModel : 'User';
			$sessionKey = isset($sessionKey) ? $sessionKey : 'Auth.User';
			$loginRedirect = isset($loginRedirect) ? $loginRedirect : array(
				'controller' => Inflector::pluralize($userModel),
				'action' => 'index'
			);
			$loginAction = isset($loginAction) ? $loginAction : array(
				'controller' => Inflector::pluralize($userModel),
				'action' => 'login',
				'admin' => false,
				'operator' => false,
				'ajax' => false
			);
			$logoutRedirect = isset($logoutRedirect) ? $logoutRedirect : '/';
			$fields = isset($fields) ? $fields : array();
			$authenticateScope = isset($authenticateScope) ? $authenticateScope : array(
				$userModel . '.active' => 1
			);
			$this->Auth->authenticate = array(
				'Form' => array(
					'userModel' => $userPlugin . $userModel,
					'scope' => $authenticateScope,
					'fields' => $fields
				)
			);
			$this->_authComponentUserModel = $userPlugin . $userModel;
			$this->Auth->loginAction = $loginAction;
			$this->Auth->loginRedirect = $loginRedirect;
			$this->Auth->logoutRedirect = $logoutRedirect;
			AuthComponent::$sessionKey = $sessionKey;
		}

		// AUTHCOMPPERMISSIONS
		$this->_setPermissions();

		// LANGUAGE
		$this->__setLanguage();

		return parent::beforeFilter();
	}

/**
 * beforeRender callback
 * Tasks:
 *	- BUILDNAVIGATION: Navigation builder wrapper.
 *
 * @return void
 */
	public function beforeRender(){
		// BUILDNAVIGATION
		$this->__buildNavigation();

		return parent::beforeRender();
	}


/**************************************
 ********** Utility methods ***********
 **************************************/

/**
 * Builds up the navigation based on the current logged in user
 *
 * @return void
 */
	private function __buildNavigation(){
		$user = $this->getCurrentUser('User.User');

		// Navigation is only for logged in users
		if($user){
			$navigation = array(
				'home' => array(
					'href' => array(
						'plugin' => 'user',
						'controller' => 'users',
						'action' => 'redirect_by_role',
						'admin' => false,
						'instructor' => false,
					),
					'sep' => 'after',
					'show' => __('Home'),
				)
			);

			$user = $this->getCurrentUser('User.User');

			// Instructor navigation
			if($user['User']['is_instructor']){
				$navigation = BB::extend(
					$navigation,
					array(
						'users' => array(
							'href' => array(
								'plugin' => 'user',
								'controller' => 'users',
								'action' => 'index',
								'admin' => false,
								'instructor' => true,
							),
							'sep' => 'after',
							'show' => __('Users'),
						),
						'profile' => array(
							'href' => array(
								'plugin' => 'user',
								'controller' => 'users',
								'action' => 'profile',
								'admin' => false,
								'instructor' => true,
							),
							'sep' => 'after',
							'show' => __('Profile'),
						),
					)
				);
			}
			// Normal user
			else{
				$navigation = BB::extend(
					$navigation,
					array(
						'reports' => array(
							'href' => array(
								'plugin' => 'report',
								'controller' => 'reports',
								'action' => 'index',
								'admin' => false,
								'instructor' => false,
							),
							'sep' => 'after',
							'show' => __('Reports')
						),
					)
				);
			}

			if($user['User']['is_admin']){
				$navigation = BB::extend(
					$navigation,
					array(
						'users' => array(
							'href' => array(
								'plugin' => 'user',
								'controller' => 'users',
								'action' => 'index',
								'admin' => true,
								'instructor' => false,
							),
							'sep' => 'after',
							'show' => __('Users'),
						),
						'departments' => array(
							'href' => array(
								'plugin' => 'report',
								'controller' => 'departments',
								'action' => 'index',
								'admin' => true,
								'instructor' => false,
							),
							'sep' => 'after',
							'show' => __('Departments'),
						),
					)
				);
			}

			$navigation = BB::extend(
				$navigation,
				array(
					'logout' => array(
						'href' => array(
							'plugin' => 'user',
							'controller' => 'users',
							'action' => 'logout',
							'admin' => false,
							'instructor' => false,
						),
						'show' => __('Logout'),
					),
				)
			);

			BbMenu::append(
				'Twb.navbar',
				$navigation	
			);
		}
	}

/**
 * Sets the language for the user based on its settings.
 * Currently not in use due to single language.
 *
 * @return void
 */
	private function __setLanguage(){
		$language = Configure::read('App.DefaultLanguage');
		$this->set(compact(
			'language'
		));
		return;
		
		App::uses('I18n', 'I18n');
		$i18n = I18n::getInstance();

		// Delete the config var to change the language for real
		Configure::delete('Config.language');

		// Admin area language has always
		if($this->isAdminArea() && Configure::read('App.Backend.UseDefaultLanguage')){
			$i18n->l10n->get(Configure::read('App.Backend.DefaultLanguage'));
			$language = $i18n->l10n->lang;
			$this->set(compact(
				'language'
			));
			return;
		}
		
		// Get language from url. If not set, use null for initial setting
		if(isset($this->request->params['lang'])){
			$language = $this->request->params['lang'];
		}
		elseif(isset($this->request->params['named']['lang'])){
			$language = $this->request->params['named']['lang'];
		}
		else{
			$language = null;
		}
		
		if(
			Configure::read('App.AutomaticLanguageDetection') === false &&
			!is_null(Configure::read('App.DefaultLanguage'))
		){
			$language = Configure::read('App.DefaultLanguage');
			$i18n->l10n->get($language);
			$language = $i18n->l10n->lang;
			$this->Cookie->write('language', $i18n->l10n->lang);
		}
		else{
			// First page visit
			if(
				is_null($language) &&
				!$this->Cookie->read('language')
			){
				// @OOO: Autodetect language
				// Use default language
				$language = DEFAULT_LANGUAGE;
				$i18n->l10n->get($language);
				$language = $i18n->l10n->lang;
				$this->Cookie->write('language', $i18n->l10n->lang);
			}
			// Manual language switch
			elseif(
				!$this->Cookie->read('language') ||
				isset($language)
			){
				$i18n->l10n->get($language);
				$language = $i18n->l10n->lang;
				$this->Cookie->write('language', $i18n->l10n->lang);
			}
			// Change language to cookie language
			elseif($this->Cookie->read('language')){
				$language = $i18n->l10n->get($this->Cookie->read('language'));
			}
			else{
				$this->log('Uncaught language switching case. Var export:');
				$this->log('Language: ' . var_export($language, true));
				$this->log('Cookie language: ' . var_export($this->Cookie->read('language'), true));
				$this->log('Config language: ' . var_export($this->Cookie->read('language'), true));
			}
		}
		$this->set(compact(
			'language'
		));
	}

/**
 * Sets the permission in a central place for all controllers based on name.
 *
 * @return void
 */
	private function _setPermissions(){
		switch($this->name){
			case 'Users':
				$aA = array('login', 'logout', 'redirect_by_role');
			break;
		}

		if(isset($aA)){
			$this->Auth->allow($aA);
		}
	}

/**
 * isAuthorized method for use with AuthComponent. Checks wether or not a user is allowed to enter a specific section
 *
 * @param array $user The user data to check.
 * @return bool User is allowed or not.
 */
/*
	This method only gets called if a user already has authentificated (logged in) himself.
	If we are not in the admin area and not in the instructor area the user parsed to this method is
	a normal user (or should be one). After the user is checked for existance all access should be granted
	by default no matter what. (@XXX: Maybe a security problem).
*/
	public function isAuthorized($user){
		if($this->isAdminArea()){
			if(
				isset($user['is_admin']) && $user['is_admin'] === true &&
				$this->User->find(
					'count',
					array(
						'conditions' => array(
							$this->User->alias . '.email' => $user['email'],
							$this->User->alias . '.active' => 1
						),
						'recursive' => -1
					)
				)
			){
				return true;
			}
		}
		elseif($this->isInstructorArea()){
			if(
				isset($user['is_instructor']) && $user['is_instructor'] === true &&
				$this->User->find(
					'count',
					array(
						'conditions' => array(
							$this->User->alias . '.email' => $user['email'],
							$this->User->alias . '.active' => 1
						),
						'recursive' => -1
					)
				)
			){
				return true;
			}
		}
		else{
			if(
				$this->User->find(
					'count',
					array(
						'conditions' => array(
							$this->User->alias . '.email' => $user['email'],
							$this->User->alias . '.active' => 1,
							$this->User->escapeField('is_instructor') => 0,
						),
						'recursive' => -1
					)
				)
			){
				return true;
			}
		}
		return false;
	}

/**
 * Checks if the controller has any data.
 *
 * @param array $requestTypes The allowed request types. Default is post.
 * @return bool
 */
	protected function hasData($requestTypes = array()){
		// Set to default (post) if no request type is provided
		if(empty($requestTypes) || !is_array($requestTypes)){
			$requestTypes = array('post', 'put');
		}

		// Value to determine if request is of given type
		$requestTypeFound = false;

		// Circle through them, to check it the request type is right
		foreach($requestTypes as $type){
			if($this->request->is($type)){
				$requestTypeFound = true;
				break;
			}
		}

		// Check if there is any data. Request is already valid at this point.
		return $requestTypeFound && !empty($this->request->data);
	}

/**
 * Checks if we are in "admin area" (admin prefix)
 * 
 * @return bool
 */
	protected function isAdminArea(){
		return isset($this->request->params['admin']) &&
			$this->request->params['admin'] == true;
	}

/**
 * Checks if we are in "instructor area" (instructor prefix)
 * 
 * @return bool
 */
	protected function isInstructorArea(){
		return isset($this->request->params['instructor']) &&
			$this->request->params['instructor'] == true;
	}

/**
 * Add helpers
 *
 * @param array $helpers The helpers to add to helper array.
 * @return void
 */
	protected function addHelpers($helpers){
		if(is_string($helpers)){
			$helpers = array($helpers);
		}
		$this->helpers = array_merge($this->helpers, $helpers);
	}


/**************************************
 ***** Public generalized methods *****
 **************************************/

/**
 * Change a field in the database
 *
 * @param Int The id of the entry in the database
 * @param String The field in the database
 * @return Boolean Success of the saving
 */
	public function admin_change($id = null, $field = null) {
		if (!$id || !$field) {
			$this->Session->setFlash(__('Invalid request', true));
			$this->redirect($this->request->referer());
		}
		if(!$this->{$this->modelClass}->change($id, $field)){
			$this->Session->setFlash(sprintf(__('Changing field %s failed', true), $field));
		}
		$this->redirect($this->request->referer());
	}
	
/**
 * admin_move method
 *
 * @param string $direction
 * @param string $id
 * @return void
 */
	public function admin_move($direction, $id){
		if($this->Components->attached('Position')){
			$this->Position->move($direction, $id);
		}
		$this->redirect(
			array(
				'action' => 'index',
			)
		);
	}

/**
 * Is a user logged in
 *
 * @return bool TRUE if a user is logged in. FALSE if not.
 */
	protected function userIsLoggedIn(){
		// @TODO: Implement me
		// return true;
		return $this->Auth->loggedIn();
	}

/**
 * Gets the current user from session
 *
 * @return mixed(bool[false]|array) User data or false if no user is logged in.
 */
	protected function getCurrentUser(){
		if($this->userIsLoggedIn()){
			return $this->{$this->modelClass}->getCurrentUser($this->_authComponentUserModel);
		}
		else{
			return false;
		}
	}

/**
 * Security component dump hole.
 *
 * @param string $type Type of the security issue.
 * @return void;
 * @throws ForbiddenException;
 */
	public function blackHole($type){
		// This method should not be callable by url.
		if(
			$this->here == Router::url(array('action' => 'blackHole')) &&
			Router::url($this->request->referer()) != Router::url('/')
		){
			$this->redirect('/');
			exit;
		}
		
		if(Configure::read('debug') >= 1){
			var_dump('something went wrong: ' . $type);
		}
		else{
			$this->response->statusCode(403);
			$this->Session->error(
				__('Possible security breach. Reloading page.'),
				$this->referer()
			);
			// throw new ForbiddenException(__('Please retry!'));
		}
	}
}
