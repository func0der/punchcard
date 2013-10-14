<?php
App::uses('ReportAppController', 'Report.Controller');
/**
 * Reports Controller
 *
 * @property Report $Report
 */
class ActivitiesController extends ReportAppController {

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter(){
		$this->Security->unlockedActions = array(
			'ajax_get_present_labels',
		);

		parent::beforeFilter();
	}

/**
 * Add of activities
 *
 * @return void
 */
	public function add(){
		if($this->hasData()){
			$data = $this->request->data;

			$this->Activity->create($data);

			// Check if the given data is valid before proceeding
			if($this->Activity->validates()){
				/*
				// Transform data due to FormHelper bug
				$data['ReportItem']['day'] = $data['ReportItem']['dayOfTheWeek'];
				unset($data['ReportItem']['dayOfTheWeek']);

				// Check for existing report item for given day of the week
				$queryOptions = array(
					'conditions' => array(
						$this->Activity->ReportItem->escapeField('day') => $data['ReportItem']['day'],
						$this->Activity->ReportItem->escapeField('report_id') => $data['ReportItem']['report_id'],
					),
					'recursive' => -1,
				);*/

/**
 * @XXX:	Maybe we can shorten this whole process by checking for an already
 *			present activity id.
 *			Task for optimization step.
 */

				// Get report item
				$reportItem = $this->Activity->ReportItem->getReportItem($data);

				if(!$reportItem){
					$this->Session->error(__d('report', 'Entry for the given week could not be created.'));
				}
				else{
					unset($data['ReportItem']);

					$reportItem = $this->Activity->ReportItem->find(
						'first',
						array(
							'conditions' => array(
								$this->Activity->ReportItem->escapeField() => $reportItem['ReportItem']['id'],
							),
						)
					);

					$user = $this->getCurrentUser('User.User');

					if($reportItem['Report']['user_id'] != $user['User']['id']){
						$this->Session->error(__d('report', 'The report item given does not belong to one of your reports.'));
					}

					if($reportItem['Report']['accepted']){
						$this->Session->error(__d('report', 'Report has already been accepted. Changes are not possible.'));
					}

					$data['Activity']['report_item_id'] = $reportItem['ReportItem']['id'];

					if($activity = $this->Activity->save(
						$data,
						array(
							'fieldList' => array(
								$this->Activity->alias => array(
									'report_item_id',
									'label',
									'duration'
								),
							),
							'validate' => false
						)
					)){
						$this->Activity->ReportItem->updateTotalDuration($data['Activity']['report_item_id']);
						$this->Session->success(__d('report', 'Activity has been saved.'));
					}
				}
			}
			else{
				$this->Session->error(__d('report', 'Your inputs were not accepted. Please check them again.'));
			}
		}
	}

/**
 * Delete activity
 *
 * @param int $id
 * @return void
 */
	public function delete($id){
		if(!$this->Activity->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid activity'));
		}

		$user = $this->getCurrentUser('User.User');

		$activity = $this->Activity->read(null, $id);

		$report = $this->Activity->ReportItem->Report->read(null, $activity['ReportItem']['report_id']);
		
		if($report['Report']['user_id'] == $user['User']['id']){
			if($this->Activity->delete($id)){
				$this->Session->success(__d('report', 'Activity deleted.'));
			}
			else{
				$this->Session->error(__d('report', 'Activity deletion failed.'));	
			}
		}
		else{
			$this->Session->error(__d('report', 'This activity does not belong to you.'));
		}
		$this->redirect($this->referer());
	}

/**
 * Ajax method for getting already saved labels
 *
 * @return void
 */
	public function ajax_get_present_labels(){
		if(!$this->request->is('ajax')){
			throw new NotFoundException();
		}
		// Set right layout
		$this->layout = 'ajax';

		// Add needed helpers
		$this->addHelpers(
			array(
				'Js'
			)
		);

		// Initialize labels
		$labels = array();

		if($this->hasData()){
			$data = $this->request->data;

			$labels = $this->Activity->find(
				'all',
				array(
					'conditions' => array(
						//@XXX: This seems a little to heavy. We are using LIKE instead
						/*"MATCH (" . $this->Activity->escapeField('label') . ") AGAINST ('" . $data['Activity']['label'] . "*' IN BOOLEAN MODE)",*/
						$this->Activity->escapeField('label') . ' LIKE' => '%' . $data['Activity']['label'] .'%',
					),
					'fields' => array(
						$this->Activity->escapeField('id'),
						$this->Activity->escapeField('label'),
					),
				)
			);

			// Include search string in the result to fix buggy select2 box
			$lables = array_unshift(
				$labels,
				array(
					'Activity' => array(
						'id' => -1,
						'label' => $data['Activity']['label'],
					),
				)
			);

		}

		$this->set(
			compact(
				'labels'
			)
		);
	}

}
