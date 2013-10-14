<?php
App::uses('ReportAppController', 'Report.Controller');
/**
 * Reports Controller
 *
 * @property Report $Report
 */
class ReportsController extends ReportAppController {

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter(){
		$this->Security->unlockedActions = array(
			'ajax_report_items',
		);

		parent::beforeFilter();
	}

/**
 * Listing of all available reports
 *
 * @return void
 */
	public function index(){
		// Add needed helpers
		$this->addHelpers(
			array(
				'Time',
				'Twb.TwbTable',
			)
		);

		// Get current user
		$user = $this->getCurrentUser('User.User');

		// Set validation set
		$this->Report->setValidation('report_add');

		if($this->hasData()){
			$data = $this->request->data;

			$this->Report->data = $data;
			if($this->Report->validates()){
				$this->Session->success(
					__d('report', 'Your inputs were accepted. Please wait, while your are being redirected.'),
					array(
						'action' => 'view',
						$data['Report']['week'],
						$data['Report']['year'],
					)
				);
			}
			else{
				$this->Session->error(__d('report', 'Your inputs were not accepted. Please check them and try again.'));
			}
		}

		$this->Report->recursive = -1;

		$this->paginate = array(
			'Report' => array(
				'conditions' => array(
					$this->Report->escapeField('user_id') => $user['User']['id'],
				),
				'order' => array(
					'year' => 'DESC',
					'week' => 'DESC',
				),
			),
		);

		$reports = $this->paginate();

		$this->set(
			compact(
				'reports',
				'user'
			)
		);
	}

/**
 * Here all the actions takes place. This is the home page of
 * the whole project.
 *
 * @param int $week The week number to show
 * @param int $year The year to show
 * @return void
 */
	public function view($week = null, $year = null){
		// Included used helpers
		$this->addHelpers(
			array(
				'Select2.Select2'
			)
		);

		// Check week and year
		$this->Report->checkWeekAndYear($week, $year);

		// If the given, already checked week year combination is not in user range go back to home
		if(!$this->Report->weekAndYearInUserRange($week, $year)){
			$this->redirect('/');
		}

		// Initiate deparments
		$deparments = array();

		$report = $this->Report->findByWeekAndYear($week, $year);

		// Get report items for the above found report (if found)
		if(!$report){
			// Get derpartments
			$departments = $this->Report->Department->findActive(
				'list',
				array(
					'recursive' => -1,
				)
			);
		}

		// Current day of the week
		$day = DATE_DAY_OF_THE_WEEK;

		$this->set(
			compact(
				'week',
				'year',
				'departments',
				'report',
				'day'
			)
		);
	}


/**
 * Create a new report based on given year and date
 *
 * @return void
 */
	public function add(){
		if($this->hasData()){
			$data = $this->request->data;

			// Check week and year
			$this->Report->checkWeekAndYear($data['Report']['week'], $data['Report']['year']);

			// Check if the a report for this week and year already exists
			$report = $this->Report->findByWeekAndYear($data['Report']['week'], $data['Report']['year']);

			// If no report is present, add one
			if(!$report){
				// Get current user
				$user = $this->getCurrentUser('User.User');
				// Attache user id to the report
				$data['Report']['user_id'] = $user['User']['id'];

				if($this->Report->save($data)){
					// Generate report items (days) for the report.
					for($i = 1; $i <= 7; $i++){
						$tempData = array(
							'ReportItem' => array(
								'dayOfTheWeek' => $i,
								'report_id' => $this->Report->getLastInsertID(),
							)
						);
						$this->Report->ReportItem->create();
						$this->Report->ReportItem->getReportItem($tempData);
					}
					$this->Session->success(__d('report', 'Report has been created.'));
				}
				else{
					$this->Session->error(__d('report', 'Report could not be created. Please, try again.'));
				}
			}
			else{
				$this->Session->error(__d('report', 'Report already present'));
			}
		}
		$this->redirect($this->request->referer());
	}


/**
 * Download an accepted report's pdf
 *
 * @param int $id Id of the report.
 * @return void
 */
	public function download($id){
		if(!$this->Report->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		$user = $this->getCurrentUser('User.User');

		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
					$this->Report->escapeField('accepted') => 1,
					$this->Report->escapeField('user_id') => $user['User']['id'],
				),
				'recursive' => -1
			)
		);

		if(!$report){
			throw new NotFoundException(__d('report', 'This report was not yet accepted.'));
		}

		$this->__generateReportPdf($id);

		exit;
	}

/**
 * Generate the report pdf
 *
 * @param int $id Id of the report to generate pdf for.
 * @return void
 */
	private function __generateReportPdf($id){
		// Get report data
		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
				),
				'recursive' => 1
			)
		);

		// Get report items
		$reportItems = $this->Report->ReportItem->find(
			'all',
			array(
				'conditions' => array(
					$this->Report->ReportItem->escapeField('report_id') => $id,
				),
				'order' => array(
					$this->Report->ReportItem->escapeField('day') => 'ASC',
				),
			)
		);

		// Get current user
		$user = $this->getCurrentUser('User.User');

		// Get used vendors
		// App::uses('tcpdf\tcpdf', 'Report.Vendor');
		App::import('Vendor', 'Report.tcpdf/tcpdf');
		App::import('Vendor', 'Report.fpdi/fpdi');

		$xs = array(
			'week' => 133,
			'name' => 67,
			'department' => 67,
			'dateRange' => array(
				'from' => 67,
				'to' => 104,
			),
			'apprenticeshipYear' => 167,
			'activity' => 35,
			'activityDuration' => 173,
			'reportItemDuration' => 186,
			'reportDuration' => 186,
			'signature' => 79,
		);

		$ys = array(
			'week' => 10,
			'name' => 20,
			'department' => 25,
			'dateRange' => array(
				'from' => 33,
				'to' => 33,
			),
			'apprenticeshipYear' => 33,
			'reportItem' => array(
				0 => 50,
				1 => 88.5,
				2 => 127,
				3 => 165.5,
				4 => 204,
			),
			'reportItemDuration' => array(
				0 => 82,
				1 => 120.5,
				2 => 159,
				3 => 197.5,
				4 => 236,
			),
			'reportDuration' => 242,
			'signature' => 258
		);

		// create new PDF document
		$pdf = new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		App::uses('I18n', 'I18n');
		$i18n = new I18n();
		$lang = $i18n->l10n->lang;

		$templateFile = App::pluginPath('Report') . 'Drafts' . DS . 'report_draft_' . $lang . '.pdf';

		$pdf->setSourceFile($templateFile);

		$templatePage = $pdf->importPage(1);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($user['User']['label']);
		$pdf->SetTitle(
			__d(
				'report',
				'Report for week %s in year %s',
				$report['Report']['week'],
				$report['Report']['year']
			)
		);

		$l = Array();

		// PAGE META DESCRIPTORS --------------------------------------

		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'de';

		// TRANSLATIONS --------------------------------------
		$l['w_page'] = 'Seite';
		$pdf->setLanguageArray($l);

		// Disable header and footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// add a page
		$pdf->AddPage();

		// Set template
		$pdf->useTemplate($templatePage);

		// Write week
		$pdf->SetFont('helvetica', 'B', 16);
		$pdf->setXY($xs['week'], $ys['week']);

		$pdf->Write(16, $report['Report']['week']);

		// Write name
		$pdf->SetFont('helvetica', '', 10);
		$pdf->setXY($xs['name'], $ys['name']);

		$pdf->Write(14, $user['User']['label']);

		// Write department
		$pdf->SetFont('helvetica', '', 10);
		$pdf->setXY($xs['department'], $ys['department']);

		$pdf->Write(14, $report['Department']['label']);

		// Write report date range
		App::uses('CakeTime', 'Utility');

		$pdf->SetFont('helvetica', '', 10);

		// From
		$pdf->setXY($xs['dateRange']['from'], $ys['dateRange']['from']);

		$pdf->Write(
			14,
			CakeTime::format(
				$reportItems[0]['ReportItem']['date'],
				__c(
					'default_date_format',
					CAKE_LC_TIME
				)
			)
		);

		// To
		$pdf->setXY($xs['dateRange']['to'], $ys['dateRange']['to']);
		$lastReportItem = end($reportItems);
		$pdf->Write(
			14,
			CakeTime::format(
				$lastReportItem['ReportItem']['date'],
				__c(
					'default_date_format',
					CAKE_LC_TIME
				)
			)
		);

		// Apprenticeship year
		$pdf->SetFont('helvetica', '', 10);
		$pdf->setXY($xs['apprenticeshipYear'], $ys['apprenticeshipYear']);

		$pdf->Write(
			14,
			$this->Report->User->calculateApprenticeshipYear(
				$user['User']['entry_date']
			)
		);

		// Output the activities
		$pdf->SetFont('helvetica', '', 10);
		$pdf->setX($xs['activity']);
		$lineHeight = 14;

		// Sum reports total duration
		$totalDuration = 0;

		foreach($reportItems as $reportItemIndex => $reportItem){
			// Set cords to report item beginning
			$pdf->setY($ys['reportItem'][$reportItemIndex]);

			foreach($reportItem['Activity'] as $activity){
				$pdf->setX($xs['activity']);
				// Activity label
				$pdf->Write($lineHeight, '- ' . $activity['label']);

				// Activity duration
				$pdf->setX($xs['activityDuration']);
				$pdf->Write($lineHeight, $activity['duration']);

				// New line
				$pdf->Ln(5);
			}
			// Report item duration
			$pdf->setXY($xs['reportItemDuration'], $ys['reportItemDuration'][$reportItemIndex]);
			$pdf->Write(
				$lineHeight,
				$this->Report->ReportItem->Activity->decimalToTime($reportItem['ReportItem']['duration'])
			);

			$totalDuration += $reportItem['ReportItem']['duration'];
		}

		// Output the report's total duration
		$pdf->SetFont('helvetica', '', 10);
		$pdf->setXY($xs['reportDuration'], $ys['reportDuration']);
		$pdf->Write(
			14,
			$this->Report->ReportItem->Activity->decimalToTime($totalDuration)
		);

		// set certificate file
		$signatureData = $this->Report->User->getSignatureData($user['ParentUser']['id']);
		if($signatureData){
			$certificate = 'file://' . $signatureData['cert'];

			$certInfo = array(
				'Name' => $user['ParentUser']['label'],
				'Reason' => __d('report', 'Report accepted.'),
			);

			// set document signature
			$pdf->setSignature($certificate, $certificate, '', '', 1, $certInfo);
			// define active area for signature appearance
			$pdf->setSignatureAppearance($xs['signature'], $ys['signature'], 59, 12);
			// Add signature image
			$signatureImage = $signatureData['image'];

			$pdf->Image(
				$signatureImage,
				$xs['signature'],
				($ys['signature']),
				59,
				12,
				'GIF'
			);
		}

		//Close and output PDF document
		$pdf->Output('Report_' . $report['Report']['year'] . 'W' . $report['Report']['week'] . '.pdf', 'D');
	}


/**
 * Publish / Unpublish a report
 *
 * @return void
 */
	public function publish_change($id){
		if(!$this->Report->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		$user = $this->getCurrentUser('User.User');

		// Check if this belongs to the user
		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
					$this->Report->escapeField('user_id') => $user['User']['id'],
				),
				'recursive' => -1,
			)
		);

		if(!$report || $report['Report']['accepted']){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		$field = 'published';

		if($this->Report->change($id, $field)){
			$this->Session->success(__d('report', 'Report has been successfully (un-)published.'));
		}
		else{
			$this->Session->error(__d('report', 'There has been an error (un-)publishing the report.'));
		}

		$this->redirect($this->request->referer());
	}


/*************************
### INSTRUCTOR METHODS ###
*************************/

/**
 * Instructor index page. It shows all entries from associated
 * Users
 *
 * @return void
 */
	public function instructor_index(){
		$this->addHelpers(
			array(
				'Twb.TwbTable'
			)
		);

		$user = $this->getCurrentUser('User.User');

		App::uses('Set', 'Utility');

		$this->paginate = array(
			$this->Report->alias => array(
				'conditions' => array(
					$this->Report->escapeField('user_id') => Set::extract(
						'/id',
						$user['ChildUser']
					),
				),
				'order' => array(
					'year' => 'DESC',
					'week' => 'DESC',
				),
			),
		);

		$reports = $this->paginate();

		$this->set(
			compact(
				'reports'
			)
		);
	}

/**
 * Instructor view page.
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function instructor_view($id = null){
		// $this->uses = array(
		// 	'Report.Report',
		// 	'Report.ReportItem',
		// );

		if(!$this->Report->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid report'));
		}

		$user = $this->getCurrentUser('User.User');

		App::uses('Set', 'Utility');

		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
					$this->Report->escapeField('user_id') => Set::extract(
						'/id',
						$user['ChildUser']
					),
				),
			)
		);

		if(!$report){
			throw new NotFoundException(__d('report', 'Invalid report'));
		}

		if($report['Report']['published'] && $this->hasData()){
			$data = $this->request->data;
			// Remove submit button from data. It causes errors while validation
			unset($data['submit']);

			// Save report data for later use
			if(isset($data['Report'])){
				$reportData = $data['Report'];
				unset($data['Report']);
			}

			// Filter those which are not part of current report
			if($this->Report->ReportItem->saveMany(
				$data,
				array(
					'deep' => true
				)
			)){
				if($reportData['review'] != intval($report['Report']['review'])){
					// Mark report for review
					if($this->Report->review($report['Report']['id'])){
						$this->Session->success(
							__d('report', 'Comments have been saved and report has been ' . $translationAddition . 'marked for review.'),
							array(
								'action' => 'index',
							)
						);
					}
					else{
						$this->Session->error(__d('report', 'There has been an error ' . $translationAddition . 'marking the report for review.'));
					}
				}
				$this->Session->success(
					__d('report', 'Comments has been successfully saved.'),
					array(
						'action' => 'index',
					)
				);
			}
			else{
				// debug($this->Report->validationErrors);
				// debug($this->Report->ReportItem->validationErrors);
				// debug($this->Report->ReportItem->Comment->validationErrors);
				$this->Session->error(__d('report', 'There has been an error saving the comments.'));
			}
		}
		elseif(!$report['Report']['published']){
			$this->Session->error(__d('report', 'This report has not been published yet. Your changes will not be saved.'));
		}

		$this->request->data = $reportItems = $this->Report->ReportItem->find(
			'all',
			array(
				'conditions' => array(
					$this->Report->ReportItem->escapeField('report_id') => $report['Report']['id'],
				),
			)
		);

		$this->set(
			compact(
				'report',
				'reportItems'
			)
		);
	}


/**
 * Accept a report
 *
 * @return void
 */
	public function instructor_accept($id){
		// @TODO: CAN NOT accept a report until he has upload a signature.
		if(!$this->Report->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		// Only accept those reports that are published
		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
					$this->Report->escapeField('published') => 1
				),
				'recursive' => -1
			)
		);	

		if(!$report){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		if($this->Report->accept($report['Report']['id'])){
			$this->Session->success(__d('report', 'Report has been successfully accepted.'));
		}
		else{
			$this->Session->error(__d('report', 'There has been an error accepting the report.'));
		}

		$this->redirect($this->request->referer());
	}


/**
 * Reject a report
 *
 * @return void
 */
	public function instructor_reject($id){
		if(!$this->Report->exists($id)){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		// Only reject those reports which where first accepted
		$report = $this->Report->find(
			'first',
			array(
				'conditions' => array(
					$this->Report->escapeField() => $id,
					$this->Report->escapeField('accepted') => 1
				),
				'recursive' => -1
			)
		);

		if(!$report){
			throw new NotFoundException(__d('report', 'Invalid report.'));
		}

		$this->Report->id = $report['Report']['id'];

		if($this->Report->saveField('accepted', 0)){
			$this->Session->success(__d('report', 'Report has been successfully rejected.'));
		}
		else{
			$this->Session->error(__d('report', 'There has been an error rejecting the report.'));
		}

		$this->redirect($this->request->referer());
	}


/*************************
###### AJAX METHODS ######
*************************/

/**
 * Ajax method for the deliverance of report items in html format
 *
 * @param int $week The week number to show
 * @param int $year The year to show
 * @return void
 */
	public function ajax_report_items($week = null, $year = null){
		if(!$this->request->is('ajax')){
			throw new NotFoundException();
		}
		// Set right layout
		$this->layout = 'ajax';

		// Include used helpers
		$this->addHelpers(
			'Time'
		);

		// Check week and year
		$this->Report->checkWeekAndYear($week, $year);

		$report = $this->Report->findByWeekAndYear($week, $year); 

		if($report){
			$options = array(
				'conditions' => array(
					$this->Report->ReportItem->escapeField('report_id') => $report['Report']['id']
				),
				'order' => array(
					$this->Report->ReportItem->escapeField('day') . ' DESC',
				),
				'recursive' => 1
			);

			$reportItems = $this->Report->ReportItem->find(
				'all',
				$options
			);
		}

		$this->set(
			compact(
				'report',
				'reportItems'
			)
		);
	}

}
