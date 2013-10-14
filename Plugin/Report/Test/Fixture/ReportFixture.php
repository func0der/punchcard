<?php
/**
 * ReportFixture
 *
 */
class ReportFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'week' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 6),
		'published' => array('type' => 'boolean', 'null' => true, 'default' => null, 'key' => 'index'),
		'accepted' => array('type' => 'boolean', 'null' => true, 'default' => null, 'key' => 'index'),
		'review' => array('type' => 'boolean', 'null' => true, 'default' => null, 'key' => 'index'),
		'department_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'week_year' => array('column' => array('week', 'year'), 'unique' => 0),
			'published' => array('column' => 'published', 'unique' => 0),
			'accepted' => array('column' => 'accepted', 'unique' => 0),
			'review' => array('column' => 'review', 'unique' => 0),
			'department_id' => array('column' => 'department_id', 'unique' => 0)
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
			'user_id' => 1,
			'week' => 1,
			'year' => 1,
			'published' => 1,
			'accepted' => 1,
			'review' => 1,
			'department_id' => 1
		),
	);

}
