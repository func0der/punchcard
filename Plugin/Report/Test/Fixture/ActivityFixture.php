<?php
/**
 * ActivityFixture
 *
 */
class ActivityFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'report_item_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'label' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'duration' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => 10),
		'position' => array('type' => 'integer', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'report_item_id' => array('column' => array('report_item_id', 'position'), 'unique' => 0),
			'label' => array('column' => 'label', 'type' => 'fulltext')
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
			'report_item_id' => 1,
			'label' => 'Lorem ipsum dolor sit amet',
			'duration' => 1,
			'position' => 1
		),
	);

}
