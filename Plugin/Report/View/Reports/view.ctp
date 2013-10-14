<?php
	$this->extend('Twb./Template/internal');
	// Include select2 css and js since we need it in both cases
	$this->Select2->includeScripts();
?>

	<h2>
	<?php
		echo __d(
			'report',
			'Week %s in %s',
			$week,
			$year
		);
	?>
	</h2>

<?php
	// If we have no report ready for this week of the year display a creation button
	if(!$report):
?>
	<div class="information">
	<?php
		echo __d(
			'report',
			'No entries for week %s in year %s.',
			$week,
			$year
		);
	?>
	</div>
	<?php
		// Create report creation form
		echo $this->Form->create(
			'Report',
			array(
				'sticky' => false,
				'url' => array(
					'plugin' => 'report',
					'controller' => 'reports',
					'action' => 'add',
				),
			)
		);
		echo $this->Form->input(
			'week',
			array(
				'type' => 'hidden',
				'value' => $week,
			)
		);
		echo $this->Form->input(
			'year',
			array(
				'type' => 'hidden',
				'value' => $year,
			)
		);
		echo $this->Form->input(
			'department_id',
			array(
				'div' => array(
					'class' => 'fLeft control-group'
				),
				'empty' => __d('report', 'Choose a department'),
				'label' => false,
				'options' => $departments,
				'placeholder' => __d('report', 'Choose a department'),
			)
		);
		// Make a fancy select out of the department selector
		$this->Select2->select2(
			array(
				'#ReportDepartmentId'
			)
		);

		// @TODO: Add ajax to this link
		echo $this->TwbLink->linkIco(
			__d('report', 'Create report'),
			array(
			),
			array(
				'class' => array(
					'fLeft',
					'btn',
					'addButton',
				),
				'icon' => 'plus',
				'onclick' => "document.getElementById('ReportViewForm').submit(); return false;",
			)
		);

		echo $this->Form->end();
	?>
	</div>
<?php
	// Else show options to manage this report
	else:
		$this->Html->scriptStart(
			array(
				'inline' => false
			)
		);
?>
		function getReportData(){
			return {
				reportYear: '<?php echo $year; ?>',
				reportWeek: '<?php echo $week; ?>',
				ajaxUrls: {
					html: '<?php
						echo $this->Html->url(
							array(
								'plugin' => 'report',
								'controller' => 'reports',
								'action' => 'report_items',
								'ajax' => true,
								$week,
								$year,
							)
						);
					?>'
				}
			}
		}

		function getActivityLabelData(){
			return {
				ajaxUrls: {
					json: '<?php
						echo $this->Html->url(
							array(
								'plugin' => 'report',
								'controller' => 'activities',
								'action' => 'get_present_labels',
								'ajax' => true
							)
						);
					?>'
				}
			}
		}
<?php
		echo $this->Html->scriptEnd();

		echo $this->Html->script(
			'Report.frontend/view.js',
			array(
				'inline' => false,
			)
		);

		echo $this->Html->css(
			'Report.frontend/view.css',
			array(
				'inline' => false,
			)
		);

		echo $this->Html->link(
			(
				$report['Report']['published'] ?
				__d('report', 'Unpublish') :
				__d('report', 'Publish')
			),
			array(
				'action' => 'publish_change',
				$report['Report']['id'],
			),
			array(
				'class' => 'reportPublishLink'
			)
		);

		echo $this->Form->create(
			'Activity',
			array(
				'url' => array(
					'plugin' => 'report',
					'controller' => 'activities',
					'action' => 'add',
				),
			)
		);

		echo $this->Form->input(
			'id',
			array(
				'type' => 'hidden',
			)
		);
		$this->Form->unlockField('Activity.id');
		
		echo $this->Form->input(
			'department_id',
			array(
				'type' => 'hidden',
				'value' => $report['Report']['department_id'],
			)
		);
		// @TODO: In case SecurityComponent comes back to work with ajax this fields needs to unlocked
		echo $this->Form->input(
			'ReportItem.id',
			array(
				'type' => 'hidden',
			)
		);
		$this->Form->unlockField('ReportItem.id');
		// @TODO: In case SecurityComponent comes back to work with ajax this fields needs to unlocked
		echo $this->Form->input(
			'ReportItem.dayOfTheWeek',
			array(
				'type' => 'hidden',
				'value' => $day,
			)
		);
		$this->Form->unlockField('ReportItem.dayOfTheWeek');
		
		echo $this->Form->input(
			'ReportItem.report_id',
			array(
				'type' => 'hidden',
				'value' => $report['Report']['id'],
			)
		);
		
		echo $this->Form->input(
			'label',
			array(
				'label' => __d('report', 'Task'),
				'placeholder' => __d('report', 'Enter a task'),
				// 'type' => 'hidden',
			)
		);

		echo $this->Form->input(
			'duration',
			array(
				'label' => __d('report', 'Duration'),
				// Bug of the TWB helper
				'append' => '<span class="add-on">' . __d('report', 'hour(s)') . '</span>',
			)
		);

		echo $this->Form->end(__d('report', 'Submit'));
?>
	<div id="reportItemsHolder">
	</div>
<?php
	endif;
?>