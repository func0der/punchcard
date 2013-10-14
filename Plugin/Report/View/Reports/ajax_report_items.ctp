<?php
	if(!empty($reportItems)):
?>
	<ul class="resettedList reportItems">
	<?php
		foreach($reportItems as $reportItem):
			$liClasses = 'reportItem';
			if($this->Time->isToday($reportItem['ReportItem']['date']) || $reportItem == end($reportItems)){
				$liClasses .= ' curDay';
			}
	?>
		<li class="<?php echo $liClasses; ?>">
		<?php
			echo $this->Form->input(
				'ReportItem.id',
				array(
					'class' => 'reportItemIdHolder',
					'type' => 'hidden',
					'value' => $reportItem['ReportItem']['id'],
				)
			);
		?>
			<div class="fLeft date">
			<?php
				echo $this->element(
					'Report.DateRepresentation',
					array(
						'date' => $reportItem['ReportItem']['date'],
					)
				);
			?>
			</div>
		<?php
			echo $this->Html->link(
				__d('report', 'Edit'),
				'#',
				array(
					'class' => 'reportItemEditToggle',
				)
			);
		?>
			<div class="activities">
			<?php
				if(!empty($reportItem['Activity'])):
			?>
				<ul class="resettedList">
				<?php
					foreach($reportItem['Activity'] as $activity):
				?>
					<li>
					<?php
						echo $this->Form->input(
							'Activity.id',
							array(
								'class' => 'activityIdHolder',
								'type' => 'hidden',
								'value' => $activity['id'],
							)
						);
					?>
						<div class="fLeft label">
						<?php
							echo $activity['label'];
						?>
						</div>
						<div class="fLeft duration">
						<?php
							echo $activity['duration'];
						?>
						</div>
					<?php
						echo $this->Html->link(
							__d('report', 'Edit'),
							'#',
							array(
								'class' => 'activityEditToggle'
							)
						);
						// @TODO: Add delete link
						echo $this->Html->link(
							__d('report', 'Delete'),
							array(
								'plugin' => 'report',
								'controller' => 'activities',
								'action' => 'delete',
								$activity['id'],
								'admin' => false,
								'ajax' => false,
								'instructor' => false
							),
							array(
								'class' => 'activityDeleteToggle',
							)
						);
					?>
						<div class="fClear"></div>
					</li>
				<?php
					endforeach;
				?>
				</ul>
			<?php
				else:
					echo __d('report', 'There are currently no activities for this day.');
				endif;
			?>
			</div>
			<div class="fRight">
				<div class="fLeft totalDuration">
				<?php
					echo ReportSupporter::decimalToTime($reportItem['ReportItem']['duration']);
				?>
				</div>
				<div class="fLeft workingHoursDifference">
				<?php
					echo $this->element(
						'Report.WorkingHoursDifference',
						array(
							'timeWorked' => $reportItem['ReportItem']['duration'],
						)
					);
				?>
				</div>
			</div>
			<div class="fClear"></div>
		</li>
	<?php
		endforeach;
	?>
	</ul>
<?php
	else:
		echo __d('report', 'There are currently no entries for this week.');
	endif;
	exit;
?>