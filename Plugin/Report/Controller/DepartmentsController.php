<?php
App::uses('ReportAppController', 'Report.Controller');
/**
 * Departments Controller
 *
 * @property Department $Department
 */
class DepartmentsController extends ReportAppController {

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Department->recursive = 0;
		$this->set('departments', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Department->exists($id)) {
			throw new NotFoundException(__d('report', 'Invalid department'));
		}
		$options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
		$this->set('department', $this->Department->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Department->create();
			if ($this->Department->save($this->request->data)) {
				$this->Session->setFlash(__d('report', 'The department has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('report', 'The department could not be saved. Please, try again.'));
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
		if (!$this->Department->exists($id)) {
			throw new NotFoundException(__d('report', 'Invalid department'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Department->save($this->request->data)) {
				$this->Session->setFlash(__d('report', 'The department has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('report', 'The department could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
			$this->request->data = $this->Department->find('first', $options);
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Department->id = $id;
		if (!$this->Department->exists()) {
			throw new NotFoundException(__d('report', 'Invalid department'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Department->delete()) {
			$this->Session->setFlash(__d('report', 'Department deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('report', 'Department was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
