<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'forename' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'surname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'entry_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index'),
		'is_instructor' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'is_admin' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'department_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'email_UNIQUE' => array('column' => 'email', 'unique' => 1),
			'is_instructor' => array('column' => 'is_instructor', 'unique' => 0),
			'is_admin' => array('column' => 'is_admin', 'unique' => 0),
			'active' => array('column' => 'active', 'unique' => 0),
			'department_id' => array('column' => 'department_id', 'unique' => 0),
			'tree' => array('column' => array('parent_id', 'lft', 'rght'), 'unique' => 0),
			'entry_date' => array('column' => 'entry_date', 'unique' => 0)
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
			'email' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'forename' => 'Lorem ipsum dolor sit amet',
			'surname' => 'Lorem ipsum dolor sit amet',
			'entry_date' => '2013-05-12',
			'is_instructor' => 1,
			'is_admin' => 1,
			'active' => 1,
			'department_id' => 1,
			'parent_id' => 1,
			'lft' => 1,
			'rght' => 1
		),
	);

}
