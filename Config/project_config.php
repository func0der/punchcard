<?php
/**
 * This file contains project specific configurations.
 * May overwrite default application settings.
 */
	$config = array(
		'Report' => array(
			'PDF' => array(
				'SavePath' => CACHE . 'reports' . DS,
				'SignaturePath' => CACHE . 'signatures' . DS
			),
		),
		'Time' => array(
			/**
			 * ISO-8601 based
			 *
			 * Monday = 0
			 * Tuesday = 1
			 * Wednesday = 2
			 * Thursday = 3
			 * Friday = 4
			 * Saturday = 5
			 * Sunday = 6
			 */
			'FirstDayOfTheWeek' => 0,
			'WorkingHours' => 8
		),
	);