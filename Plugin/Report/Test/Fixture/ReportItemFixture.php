<?php
/**
 * ReportItemFixture
 *
 */
class ReportItemFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'report_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'day' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'key' => 'index'),
		'duration' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => 10),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'day' => array('column' => 'day', 'unique' => 0),
			'report_id' => array('column' => 'report_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'report_id' => 1,
			'day' => 1,
			'duration' => 1
		),
	);

}
