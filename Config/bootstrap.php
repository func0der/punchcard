<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$minPhpVersion = '5.3.0';

if(version_compare(PHP_VERSION, $minPhpVersion, '<')){
	throw new Exception('PHP version >= 5.3 is need to run this applicaion.');
}

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

/**
 * In diffrent system LC_TIME and other localiing constants have
 * different values. This is why you can not use them as constants
 * in cakephps translation methods.
 * We use a dirty reflection hack which is only working with PHP >= 5.3
 * to relfect cakephps I18n class, find translation categories and
 * prefix them with "CAKE_" to use them in clear context within the
 * code.
 * @throws Exception In case ReflectionProperty::setAccessible()
 *						is not present.
 */
if(method_exists('ReflectionProperty', 'setAccessible')){
	// Generate a php conform list for translation categories
	App::uses('I18n', 'I18n');
	// Get i18n instance
	$i18n = I18n::getInstance();
	// Get reflection of the class
	$i18nReflection = new ReflectionClass($i18n);
	// Get protected _categories property
	$prop = $i18nReflection->getProperty('_categories');
	// Make it accessible
	$prop->setAccessible(true);
	// Get the value of the categories
	$oldCategories = $prop->getValue($i18n);
	// Generate new categories array
	foreach($oldCategories as $index => $cat){
		define('CAKE_' . $cat, $index);
	}
	// Clean up
	unset($i18n);
	unset($i18nReflection);
	unset($prop);
	unset($oldCategories);
}
else{
	throw new Exception('ReflectionProperty::setAccessible() could not be found. Please check php version.');
}

/**
 * Set some time constants which could become useful all over
 * the application.
 *
 * @const string SQL_DATE_FORMAT				Sql conform date format
 * @const string SQL_DATETIME_FORMAT			Sql conform date time format
 * @const string TIME_NOW						Timestamp of the current date time
 * @const string TIME_FIRST_OF_CURRENT_MONTH	Timestamp of first day of current month at 00:00:00
 * @const string TIME_LAST_OF_CURRENT_MONTH		Timestamp of last day of current month at 23:59:59
 * @const int DATE_YEAR							The current year
 * @const int DATE_WEEK							The current week of the year (0-53)
 * @const int DATE_DAY_OF_THE_WEEK				The current day of the week after ISO-8601 (1(Monday)-7(Sunday))
 */
define('SQL_DATE_FORMAT', '%Y-%m-%d');
define('SQL_DATETIME_FORMAT', '%Y-%m-%d %H:%M:%S');
define('TIME_NOW', strtotime('NOW'));
define('TIME_FIRST_OF_CURRENT_MONTH', strtotime('first day of this month 00:00:00'));
define('TIME_LAST_OF_CURRENT_MONTH', strtotime('last day of this month 23:59:59'));
define('DATE_YEAR', strftime('%Y'));
define('DATE_WEEK', strftime('%V'));
define('DATE_DAY_OF_THE_WEEK', strftime('%u'));


/**
 * Load config needed for the application itself.
 */
Configure::load('config');

/**
* BB
* You only need to load BB plugin with bootstrap.
* Other plugins are loaded automagically by BB.
*/
CakePlugin::load( 'BB', array('bootstrap'=> true, 'routes' => true) );

/**
 * Include project specific configuration. It maybe
 * replaces default values in the apllication config.
 */
Configure::load('project_config');

/**
 * Define default language settings based on config.
 */
// Set default language constant
define('DEFAULT_LANGUAGE', Configure::read('App.DefaultLanguage'));
// Write default language in the config array to use it later on.
Configure::write('Config.language', Configure::read('App.DefaultLanguage'));


// Disable ajax forms
// BB::write('Twb.layout.disable.ajaxForm', true);
// Configure navbar
BB::write('Twb.layout.config.menu.position', 'left');