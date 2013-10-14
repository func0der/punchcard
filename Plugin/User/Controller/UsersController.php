<?php
App::uses('UserAppController', 'User.Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends UserAppController {

/**
 * Constructor
 *
 * @see Controller::__construct()
 * @return void
 */
	public function __construct($request = null, $response = null){
		// $this->components = array_merge(
		// 	$this->components,
		// 	array(
		// 		'Security' => array(
		// 			'blackHoleCallback' => 'blackHole',
		// 		),
		// 	)
		// );

		parent::__construct($request, $response);
	}

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter(){
		// Disable ajax forms if it is not login form
		if(
			!in_array(
				$this->action,
				array(
					'login'
				)
			)
		){
			BB::write('Twb.layout.disable.ajaxForm', true);
		}

		parent::beforeFilter();
	}
	

/**
 * login method
 *
 * @return void
 */
	public function login() {
		if(!$this->getCurrentUser('User.User')){
			$this->User->setValidation('user_login');
			if($this->hasData()){
				$data = $this->request->data;
				$this->User->data = $data;
				
				if($this->User->validates() && $this->Auth->login()){
					$this->Session->success(
						__d('user', 'Login successful'),
						$this->Auth->redirect()
					);
				}
				else{
					$this->Session->error(__d('user', 'Email or password is wrong.'));
				}
			}
		}
		else{
			$this->redirect(
				$this->Auth->loginRedirect
			);
		}
	}

/**
 * logout method
 *
 * @return void
 */
	public function logout(){
		$this->redirect($this->Auth->logout());
	}

/**
 * Redirect user role based
 *
 * @return void
 */
	public function redirect_by_role(){
		$user = $this->getCurrentUser('User.User');

		// Instructor redirect. Admins are also instructors
		if($user['User']['is_instructor']){
			$this->redirect(
				array(
					'plugin' => 'report',
					'controller' => 'reports',
					'action' => 'index',
					'admin' => false,
					'instructor' => true,
				)
			);
		}
		// Normal user redirect
		else{
			$this->redirect(
				array(
					'plugin' => 'report',
					'controller' => 'reports',
					'action' => 'view',
					'admin' => false,
					'instructor' => false,
				)
			);
		}
	}
	

/**
 * instructor_index method
 *
 * @return void
 */
	public function instructor_index() {
		$user = $this->getCurrentUser('User.User');

		$this->paginate = array(
			'User' => array(
				'conditions' => array(
					$this->User->alias . '.parent_id' => $user['User']['id'],
				),
			),
		);

		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * instructor_add method
 *
 * @return void
 */
	public function instructor_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			$data = $this->request->data;

			$user = $this->getCurrentUser('User.User');

			$data['User']['department_id'] = $user['User']['department_id'];
			$data['User']['parent_id'] = $user['User']['id'];

			if ($this->User->save($data)) {
				$this->Session->setFlash(__d('user', 'The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * instructor_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function instructor_edit($id = null) {
		if (!$this->User->exists($id) || !$this->User->userIsChild($id)) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}
		// Set validation set
		$this->User->setValidation('instructor_edit');

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__d('user', 'The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * instructor_profile method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function instructor_profile() {
		$user = $this->getCurrentUser('User.User');

		// Set validation set
		$this->User->setValidation('instructor_profile');

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			
			// If we do not have a new upload, validate without it
			if(!$this->_checkForFileUpload($data['User']['signature_cert'])){
				$validationSet = 'instructor_profile_withoutUpload';
				$newUpload = false;
			}
			else{
				$validationSet = 'instructor_profile';
				$newUpload = true;
			}

			$this->User->setValidation($validationSet);

			$this->User->data = $data;

			if($this->User->validates()){
				if ($this->User->save($data, array('validate' => false))) {
					if($newUpload){
						// Save signature
						if($this->User->saveSignatureData($user['User']['id'], $data)){
							$this->Session->setFlash(__d('user', 'The user and certificate has been saved'));	
						}
					}

					$this->Session->setFlash(__d('user', 'The user has been saved'));
					$this->redirect(array('action' => 'profile'));
				} else {
					$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
				}
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $user['User']['id']));
			$this->request->data = $this->User->find('first', $options);
		}

		$signatureData = $this->User->getSignatureData($user['User']['id']);

		$this->set(
			compact(
				'signatureData'
			)
		);
	}
/**
 * instructor_change_password method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function instructor_change_password($id = null) {
		$user = $this->getCurrentUser('User.User');

		if (
			!$this->User->exists($id) ||
			(
				(intval($id) !== intval($user['User']['id'])) &&
				!$this->User->userIsChild($id)
			)
		) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}

		// Set validation set
		$this->User->setValidation('instructor_change_password');

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$this->User->data = $data;

			if($this->User->validates()){

				$data[$this->User->alias]['password'] = $this->User->hashPassword($data[$this->User->alias]['new_password']);

				if ($this->User->save($data)) {
					$this->Session->setFlash(__d('user', 'The user has been saved'));
					$this->redirect(array('action' => 'index'));
				}
			}
			$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * instructor_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function instructor_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists() || !$this->User->userIsChild($id)) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__d('user', 'User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('user', 'User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			$data = $this->request->data;

			if ($this->User->save($data)) {
				$this->Session->setFlash(__d('user', 'The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}

		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$userData = $this->User->find('first', $options);

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__d('user', 'The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $userData;
		}

		App::uses('Set', 'Utility');
		$badUsers = Set::extract('/id', $userData['ChildUser']);
		array_unshift($badUsers, $id);

		$departments = $this->User->Department->find('list');
		$parents = $this->User->find(
			'list',
			array(
				'conditions' => array(
					$this->User->escapeField() . ' NOT' => $badUsers,
				),
			)
		);

		$this->set(
			compact(
				'departments',
				'parents'
			)
		);
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__d('user', 'User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('user', 'User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_change_password method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_change_password($id = null) {
		$user = $this->getCurrentUser('User.User');

		if (!$this->User->exists($id)) {
			throw new NotFoundException(__d('user', 'Invalid user'));
		}

		// Set validation set
		$this->User->setValidation('instructor_change_password');

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$this->User->data = $data;

			if($this->User->validates()){

				$data[$this->User->alias]['password'] = $this->User->hashPassword($data[$this->User->alias]['new_password']);

				if ($this->User->save($data)) {
					$this->Session->setFlash(__d('user', 'The user has been saved'));
					$this->redirect(array('action' => 'index'));
				}
			}
			$this->Session->setFlash(__d('user', 'The user could not be saved. Please, try again.'));
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**********************************************
++++++++++++++++++ Essentials +++++++++++++++++
**********************************************/

/**
 * Check for file upload
 *
 * @param array $file File data to check
 * @return bool
 */
	protected function _checkForFileUpload($file){
		return (
			(isset($file['error']) && $file['error'] == 0) ||
			(!empty( $file['tmp_name']) && $file['tmp_name'] != 'none')
		);
	}
}