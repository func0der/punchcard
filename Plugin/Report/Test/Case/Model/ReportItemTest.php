<?php
App::uses('ReportItem', 'Report.Model');

/**
 * ReportItem Test Case
 *
 */
class ReportItemTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.report.report_item',
		'plugin.report.report',
		'plugin.report.activity'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ReportItem = ClassRegistry::init('Report.ReportItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ReportItem);

		parent::tearDown();
	}

}
