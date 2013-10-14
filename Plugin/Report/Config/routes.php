<?php
/**
 * Routes for report plugin
 */

 	Router::connect(
 		'/',
 		array(
 			'plugin' => 'report',
 			'controller' => 'reports',
 			'action' => 'view'
 		)
 	);

// Home for instructors
 	Router::connect(
 		'/instructor',
 		array(
 			'plugin' => 'report',
 			'controller' => 'reports',
 			'action' => 'index',
 			'instructor' => true,
 		)
 	);